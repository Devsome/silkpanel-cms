<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class SilkroadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerSilkroadBladeComponents();
    }

    protected function registerSilkroadBladeComponents(): void
    {
        Blade::directive('onlineCounter', function () {
            return "<?php echo \\App\\View\\Components\\OnlineCounter::getData(); ?>";
        });
    }
}
