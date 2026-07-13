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

        // Latest global (yell) chat messages. iSRO only (renders nothing otherwise).
        // Usage: @globalsWidget  or  @globalsWidget(5)  or  <x-globals-widget :limit="5" />
        Blade::component('globals-widget', \App\View\Components\GlobalsWidget::class);

        Blade::directive('globalsWidget', function ($expression) {
            $expression = trim((string) $expression);
            $args = $expression === '' ? '' : "'limit' => (int) ({$expression})";

            return "<?php echo app(\\App\\View\\Components\\GlobalsWidget::class, [{$args}])->render()->render(); ?>";
        });
    }
}
