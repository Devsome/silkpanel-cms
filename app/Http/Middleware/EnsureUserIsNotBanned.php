<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBanned
{
    /**
     * Handle an incoming request.
     *
     * Logs out and blocks any authenticated user whose web account has been
     * banned (flagged via the `banned` column). This complements the check in
     * LoginRequest by kicking out users that get banned while already logged in.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user && $user->isBanned()) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => __('auth.banned'),
            ]);
        }

        return $next($request);
    }
}
