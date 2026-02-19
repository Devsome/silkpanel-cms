<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Password::defaults(function () {
            $rule = Password::min(8);

            /** @var Application $app */
            $app = $this->app;

            return $app->isProduction()
                ? $rule->mixedCase()->numbers()->uncompromised()
                : $rule;
        });
    }
}
