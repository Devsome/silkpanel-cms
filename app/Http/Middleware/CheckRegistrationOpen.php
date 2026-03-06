<?php

namespace App\Http\Middleware;

use App\Helpers\SettingHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistrationOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! SettingHelper::get('registration_open', true)) {
            return redirect()
                ->route('login')
                ->with('status', __('Registration is currently closed.'));
        }

        return $next($request);
    }
}
