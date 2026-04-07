<?php

namespace App\Providers;

use App\Services\TemplateService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class TemplateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TemplateService::class);
    }

    public function boot(): void
    {
        $this->registerTemplateViewNamespace();
        $this->registerTemplateViewComposer();
        $this->registerBladeDirective();
    }

    /**
     * Register the template view paths with proper fallback ordering.
     *
     * This adds the active template's path as the first lookup location
     * and the basic template as the fallback namespace.
     */
    protected function registerTemplateViewNamespace(): void
    {
        $templateService = $this->app->make(TemplateService::class);
        $activeTemplate = $templateService->getActiveTemplate();

        $paths = [];

        // Active template gets priority (if one is set)
        if ($activeTemplate !== null) {
            $activePath = $templateService->templatePath($activeTemplate);
            if (is_dir($activePath)) {
                $paths[] = $activePath;
            }
        }

        // Root views directory is always the fallback
        $paths[] = resource_path('views');

        // Register under the 'template' namespace: template::welcome, template::auth.login, etc.
        View::addNamespace('template', $paths);
    }

    /**
     * Share the active template name with all views.
     */
    protected function registerTemplateViewComposer(): void
    {
        View::composer('*', function ($view) {
            $templateService = app(TemplateService::class);
            $view->with('activeTemplate', $templateService->getActiveTemplate());
        });
    }

    /**
     * Register @template('view.name') blade directive for convenience.
     */
    protected function registerBladeDirective(): void
    {
        Blade::directive('templateView', function (string $expression): string {
            return "<?php echo \$__env->make('template::' . {$expression}, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
        });
    }
}
