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

            $user->setGamePassword(
                $validated['password'],
                config('silkpanel.version') === 'isro' ? $user->pjid : $user->jid,
                $tbUser,
                $muUser
            );
        } catch (\Exception $e) {
            return back()->withErrors(['password' => 'Failed to update password. Please try again.']);
        }

        return back()->with('status', 'password-updated');
    }
}
