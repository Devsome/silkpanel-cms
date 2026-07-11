<?php

namespace App\Providers;

use App\Events\WebMarket\ListingCreated;
use App\Helpers\SettingHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use SilkPanel\Discord\Listeners\DispatchDiscordNotifications;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        Model::preventLazyLoading(!app()->isProduction());

        Password::defaults(function () {
            $rule = Password::min(8);

            /** @var Application $app */
            $app = $this->app;

            return $app->isProduction()
                ? $rule->mixedCase()->numbers()->uncompromised()
                : $rule;
        });

        $this->registerBladeDirectives();
        $this->registerEventListeners();
    }

    private function registerEventListeners(): void
    {
        Event::listen(ListingCreated::class, DispatchDiscordNotifications::class);
    }

    private function registerBladeDirectives(): void
    {
        Blade::directive('settings', function (string $expression): string {
            return "<?php echo e(\\App\\Helpers\\SettingHelper::get({$expression})); ?>";
        });

        Blade::if('settingsRegistrationOpen', function (): bool {
            return (bool) SettingHelper::get('registration_open', true);
        });

        Blade::if('settingsEmailVerificationRequired', function (): bool {
            return (bool) SettingHelper::get('email_verification_required', true);
        });
    }
}
