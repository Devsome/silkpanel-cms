<?php

use App\Http\Middleware\InstallerGate;
use App\Http\Middleware\SetFrontendLocale;
use Illuminate\Foundation\Application;
use App\Http\Middleware\VerifySilkPanelApiKey;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', SetFrontendLocale::class);

        $middleware->alias([
            'silkpanel.api' => VerifySilkPanelApiKey::class,
            'installer.gate' => InstallerGate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
