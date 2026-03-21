<?php

namespace App\Http\Middleware;

use App\Helpers\SettingHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SetFrontendLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $fallbackLocale = (string) config('app.locale', 'en');
        $fallbackLocale = strtolower(str_replace('-', '_', $fallbackLocale));

        try {
            $enabledLanguages = SettingHelper::frontendLanguages();
            $frontendLanguages = SettingHelper::frontendLanguagesWithLabels();
        } catch (Throwable) {
            $enabledLanguages = [$fallbackLocale];
            $frontendLanguages = [$fallbackLocale => strtoupper($fallbackLocale)];
        }

        $defaultLocale = in_array(config('app.locale', 'en'), $enabledLanguages, true)
            ? (string) config('app.locale', 'en')
            : $enabledLanguages[0];

        $locale = (string) $request->session()->get('frontend_locale', $defaultLocale);

        if (! in_array($locale, $enabledLanguages, true)) {
            $locale = $defaultLocale;
            $request->session()->put('frontend_locale', $locale);
        }

        app()->setLocale($locale);
        view()->share('frontendLanguages', $frontendLanguages);
        view()->share('currentFrontendLocale', $locale);

        return $next($request);
    }
}
