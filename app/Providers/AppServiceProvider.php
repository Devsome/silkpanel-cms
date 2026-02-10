<?php

namespace App\Providers;

use App\Services\TemplateService;
use App\View\CustomViewFinder;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\FileViewFinder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Template Service
        $this->app->singleton(TemplateService::class);

        // Replace the default view finder with our custom one
        $this->app->singleton('view.finder', function ($app) {
            $paths = $app['config']['view.paths'];
            $finder = new CustomViewFinder($app['files'], $paths);
            $finder->setTemplateService($app->make(TemplateService::class));
            return $finder;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure templates directory exists
        $templatesPath = storage_path('app/templates');
        if (!file_exists($templatesPath)) {
            mkdir($templatesPath, 0755, true);
        }
    }
}
