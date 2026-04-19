<?php

namespace App\Providers;

use App\View\Components\DiscordWidget;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class DiscordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::component('discord-widget', DiscordWidget::class);

        Blade::directive('discordWidget', function () {
            return "<?php echo app(\\App\\View\\Components\\DiscordWidget::class)->render()->render(); ?>";
        });
    }
}
