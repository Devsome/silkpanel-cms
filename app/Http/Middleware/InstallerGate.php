<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class InstallerGate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow complete page if first-view flag exists (one-time after installation)
        if ($request->routeIs('installer.complete')) {
            $flagPath = storage_path('framework/installer-first-view.flag');
            if (File::exists($flagPath)) {
                // First view after installation - allow access
                return $next($request);
            }
        }

        if (! $this->shouldAllowInstaller()) {
            return redirect('/')->with('error', 'Installation is already complete.');
        }

        return $next($request);
    }

    /**
     * Determine if the installer should be accessible
     * Both conditions must be true: INSTALLER_ENABLED=true AND installer.lock does not exist
     */
    private function shouldAllowInstaller(): bool
    {
        $installerEnabled = $this->resolveInstallerEnabled();
        $lockExists = File::exists(base_path('installer.lock'));

        // Both conditions must be met
        return $installerEnabled && !$lockExists;
    }

    private function resolveInstallerEnabled(): bool
    {
        $value = env('INSTALLER_ENABLED', false);

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower(trim($value)), ['1', 'true', 'on', 'yes'], true);
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        return false;
    }
}
