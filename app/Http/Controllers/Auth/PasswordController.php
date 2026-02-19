<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use SilkPanel\SilkroadModels\Models\Account\AbstractTbUser;
use SilkPanel\SilkroadModels\Models\Portal\MuUser;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request, AbstractTbUser $tbUser, MuUser $muUser): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        try {
            $user = $request->user();

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            if ($tbUser instanceof \SilkPanel\SilkroadModels\Models\Account\VSRO\TbUser) {
                $tbUser->where('JID', $user->jid)->update(['password' => md5($validated['password'])]);
            } elseif ($tbUser instanceof \SilkPanel\SilkroadModels\Models\Account\ISRO\TbUser) {
                $muUser->where('JID', $user->jid)->update(['UserPwd' => md5($validated['password'])]);
                $tbUser->where('PortalJID', $user->jid)->update(['password' => md5($validated['password'])]);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['password' => 'Failed to update password. Please try again.']);
        }

        return back()->with('status', 'password-updated');
    }
}
