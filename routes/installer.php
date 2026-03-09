<?php

use App\Http\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Installer Routes
|--------------------------------------------------------------------------
|
| These routes handle the SilkPanel CMS web installation process.
| They work without requiring a complete .env file.
|
*/

// Installer routes
Route::prefix('install')->name('installer.')->group(function () {

    // Welcome page
    Route::get('/', [InstallerController::class, 'welcome'])
        ->middleware(['installer.gate'])
        ->name('welcome');

    // Environment check - minimal middleware
    Route::get('/environment', [InstallerController::class, 'environment'])
        ->middleware(['installer.gate'])
        ->name('environment');
    // SilkPanel configuration (API Key + Server Version)
    Route::get('/silkpanel', [InstallerController::class, 'silkpanel'])
        ->middleware(['installer.gate'])
        ->name('silkpanel');

    // API Key verification (AJAX)
    Route::post('/verify-api-key', [InstallerController::class, 'verifyApiKey'])
        ->middleware(['installer.gate'])
        ->name('verify-api-key');


    // Configuration - add throttling
    Route::get('/configuration', [InstallerController::class, 'configuration'])
        ->middleware(['installer.gate'])
        ->name('configuration');

    Route::post('/configuration', [InstallerController::class, 'install'])
        ->middleware(['installer.gate'])
        ->name('install');

    // Database tests (AJAX) - throttle by IP only (no user lookup)
    Route::post('/test-database', [InstallerController::class, 'testDatabase'])
        ->middleware(['installer.gate'])
        ->name('test-database');
    Route::post('/test-mssql-database', [InstallerController::class, 'testMssqlDatabase'])
        ->middleware(['installer.gate'])
        ->name('test-mssql-database');


    // Installation complete
    Route::get('/complete', [InstallerController::class, 'complete'])
        ->middleware(['installer.gate'])
        ->name('complete');
});
