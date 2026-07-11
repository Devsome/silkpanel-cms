<?php

use App\Http\Middleware\InstallerGate;
use App\Http\Middleware\SetFrontendLocale;
use Illuminate\Foundation\Application;
use App\Http\Middleware\VerifySilkPanelApiKey;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', SetFrontendLocale::class);

        $middleware->validateCsrfTokens(except: [
            'webhook/*',
            'postback/*',
        ]);

        $middleware->alias([
            'silkpanel.api' => VerifySilkPanelApiKey::class,
            'installer.gate' => InstallerGate::class,
        ]);

        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpException $e) {
            $status = $e->getStatusCode();

            if (in_array($status, [401, 402, 403, 404, 419, 429, 500, 503])) {
                return response()->view("template::errors.$status", ['exception' => $e], $status);
            }

            return null;
        });
    })->create();
