<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UsergroupRoleEnums;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidationRules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use SilkPanel\SilkroadModels\Models\Account\AbstractTbUser;
use SilkPanel\SilkroadModels\Models\Account\SkSilk;
use SilkPanel\SilkroadModels\Models\Portal\AuhAgreedService;
use SilkPanel\SilkroadModels\Models\Portal\MuEmail;
use SilkPanel\SilkroadModels\Models\Portal\MuhAlteredInfo;
use SilkPanel\SilkroadModels\Models\Portal\MuJoiningInfo;
use SilkPanel\SilkroadModels\Models\Portal\MuUser;
use SilkPanel\SilkroadModels\Models\Portal\MuVIPInfo;

class RegisteredUserController extends Controller
{
    use ValidationRules;

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, AbstractTbUser $tbUser): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'silkroad_id' => $this->usernameRules(),
            'referral' => $this->referralRules(),
        ];

        if ($tbUser instanceof \SilkPanel\SilkroadModels\Models\Account\VSRO\TbUser) {
            $rules['silkroad_id'][] = 'unique:' . \SilkPanel\SilkroadModels\Models\Account\VSRO\TbUser::class . ',StrUserID';
        } else {
            $rules['silkroad_id'][] = 'unique:' . MuUser::class . ',UserID';
            $rules['silkroad_id'][] = 'unique:' . \SilkPanel\SilkroadModels\Models\Account\ISRO\TbUser::class . ',StrUserID';
            $rules['email'][] = 'unique:' . MuEmail::class . ',EmailAddr';
        }

        $request->validate($rules);

        if ($tbUser instanceof \SilkPanel\SilkroadModels\Models\Account\VSRO\TbUser) {
            $jid = $this->createVsroAccount($request, $tbUser);
        } elseif ($tbUser instanceof \SilkPanel\SilkroadModels\Models\Account\ISRO\TbUser) {
            $jid = $this->createIsroAccount($request, $tbUser);
        } else {
            abort(500, 'Invalid AbstractTbUser instance provided.');
        }

        $referrerId = User::select('id')
            ->where('reflink', $request->referral)
            ->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

            'reflink' => Str::uuid(),
            'referrer_id' => $referrerId->id ?? null,

            'jid' => $jid,
            'silkroad_id' => $request->silkroad_id,
            'register_ip' => $request->ip(),
        ]);

        $user->assignRole(UsergroupRoleEnums::CUSTOMER);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    #region private functions

    /**
     * Create a new silkroad account based on the provided tbuser instance and request data
     *
     * @param Request $request
     * @param AbstractTbUser $tbUser
     * @return int
     */
    private function createVsroAccount(Request $request, AbstractTbUser $tbUser)
    {
        try {
            // Start transactions on both relevant connections
            DB::connection(\App\Enums\DatabaseNameEnums::SRO_ACCOUNT->value)->beginTransaction();

            $silkroadAccount = $tbUser->createAccount(
                jid: 0,
                username: $request->silkroad_id,
                password: $request->password,
                email: $request->email,
                ip: $request->ip()
            );

            SkSilk::create([
                'JID' => $silkroadAccount->JID,
                'silk_own' => 0,
                'silk_gift' => 0,
                'silk_point' => 0
            ]);

            DB::connection(\App\Enums\DatabaseNameEnums::SRO_ACCOUNT->value)->commit();
        } catch (\Exception $e) {
            DB::connection(\App\Enums\DatabaseNameEnums::SRO_ACCOUNT->value)->rollBack();
            Log::error('Failed to create Silkroad account', ['error' => $e->getMessage()]);
            abort(500, 'Failed to create Silkroad account');
        }

        return $silkroadAccount->JID;
    }

    /**
     * Create a new silkroad account based on the provided tbuser instance and request data
     *
     * @param Request $request
     * @param AbstractTbUser $tbUser
     * @return int
     */
    private function createIsroAccount(Request $request, AbstractTbUser $tbUser)
    {
        try {
            // Start transactions on both relevant connections
            DB::connection(\App\Enums\DatabaseNameEnums::SRO_PORTAL->value)->beginTransaction();
            DB::connection(\App\Enums\DatabaseNameEnums::SRO_ACCOUNT->value)->beginTransaction();

            $portalUser = MuUser::setPortalAccount(
                username: $request->silkroad_id,
                password: $request->password
            );

            $ip = filter_var($request->ip(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ?: '0.0.0.0';
            $ipLong = sprintf('%u', ip2long($ip));

            MuEmail::setEmail(
                jid: $portalUser->JID,
                email: $request->email
            );

            MuhAlteredInfo::setAlteredInfo(
                jid: $portalUser->JID,
                username: $request->silkroad_id,
                email: $request->email,
                ip: $ipLong
            );

            AuhAgreedService::setAgreedService(
                jid: $portalUser->JID,
                ip: $ipLong
            );

            MuJoiningInfo::setJoiningInfo(
                jid: $portalUser->JID,
                ip: $ipLong
            );

            // todo check if this is necessary, since the default VIP level is 0 which means no VIP benefits, it might be redundant to create a VIPInfo record for every user
            MuVIPInfo::setVIPInfo(
                jid: $portalUser->JID
            );

            $tbUser->createAccount(
                jid: $portalUser->JID,
                username: $request->silkroad_id,
                password: $request->password,
                email: $request->email,
                ip: $ip
            );

            DB::connection(\App\Enums\DatabaseNameEnums::SRO_PORTAL->value)->commit();
            DB::connection(\App\Enums\DatabaseNameEnums::SRO_ACCOUNT->value)->commit();
        } catch (\Exception $e) {
            DB::connection(\App\Enums\DatabaseNameEnums::SRO_PORTAL->value)->rollBack();
            DB::connection(\App\Enums\DatabaseNameEnums::SRO_ACCOUNT->value)->rollBack();
            Log::error('Failed to create Silkroad account', ['error' => $e->getMessage()]);
            abort(500, 'Failed to create Silkroad account');
        }

        return $portalUser->JID;
    }

    #endregion private functions
}
