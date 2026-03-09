<?php

namespace App\Http\Middleware;

use App\Enums\UsergroupRoleEnums;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilamentAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole([
            UsergroupRoleEnums::SUPPORTER->value,
            UsergroupRoleEnums::ADMIN->value
        ])) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
