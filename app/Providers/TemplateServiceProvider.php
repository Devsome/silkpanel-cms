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
        $this->registerTemplateLangPath();
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
        $activePath = null;

        try {
            $templateService = $this->app->make(TemplateService::class);
            $activeTemplate = $templateService->getActiveTemplate();

            // Active template gets priority (if one is set)
            if ($activeTemplate !== null) {
                $candidate = $templateService->templatePath($activeTemplate);
                if (is_dir($candidate)) {
                    $activePath = $candidate;
                }
            }
        } catch (\Throwable) {
            // Database not available yet (fresh install / installer not run).
        }

        $paths = array_values(array_filter([
            $activePath,
            // When a non-basic template is active, fall back to basic's views so that
            // shared partials (e.g. template::partials.navigation) are always resolvable
            // even if the active template does not override them.
            ($activePath !== null && basename($activePath) !== 'basic')
                ? resource_path('views/templates/basic')
                : null,
            resource_path('views'),
        ]));

        // Register under the 'template' namespace: template::welcome, template::auth.login, etc.
        View::addNamespace('template', $paths);

        // Also prepend the active template path to the DEFAULT view finder so that
        // Blade anonymous components (<x-app-layout>) and bare view() calls also
        // resolve template overrides first.
        // e.g. templates/my-theme/layouts/app.blade.php overrides resources/views/layouts/app.blade.php
        if ($activePath !== null) {
            View::prependLocation($activePath);
        }
    }

    /**
     * Add the active template's lang/ directory to the default translation loader.
     *
     * Lang files placed inside the template folder (e.g. lang/en/my-template.php)
     * are automatically picked up by __('my-template.key') — no namespace prefix needed.
     * This means gitignoring the whole template folder also excludes its translations.
     */
    protected function registerTemplateLangPath(): void
    {
        try {
            $templateService = $this->app->make(TemplateService::class);
            $activeTemplate  = $templateService->getActiveTemplate();

            if ($activeTemplate === null) {
                return;
            }

            $langPath = $templateService->templatePath($activeTemplate) . '/lang';

            if (is_dir($langPath)) {
                /** @var \Illuminate\Translation\FileLoader $loader */
                $loader = $this->app['translation.loader'];
                $loader->addPath($langPath);
            }
        } catch (\Throwable) {
            // Database not available yet or template not found — skip silently.
        }
    }

    /**
     * Share the active template name with all views.
     */
    protected function registerTemplateViewComposer(): void
    {
        View::composer('*', function ($view) {
            try {
                $templateService = app(TemplateService::class);
                $view->with('activeTemplate', $templateService->getActiveTemplate());
            } catch (\Throwable) {
                $view->with('activeTemplate', null);
            }
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

        // Outputs the Vite <link> tag for the active template's assets/app.css (if present).
        // Usage: add @templateStyles inside <head> of every base layout.
        Blade::directive('templateStyles', function (): string {
            return "<?php echo app(\App\Services\TemplateService::class)->renderTemplateStyles(); ?>";
        });
    }
}
