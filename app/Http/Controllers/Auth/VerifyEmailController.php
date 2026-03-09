<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            if (SettingHelper::get('email_verification_required')) {
                $request->user()->muUser?->muAlteredInfo->update(['EmailReceptionStatus' => 'Y', 'EmailCertificationStatus' => 'Y']);
            }
        }

        return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }
}
