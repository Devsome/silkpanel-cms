<?php

use App\Http\Controllers\Api\MapController;
use App\Http\Middleware\FilamentAdminMiddleware;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by bootstrap/app.php and all of them will be
| assigned the "api" middleware group. They require a logged-in user
| via the web session guard.
|
*/

Route::middleware(['web', 'auth', 'verified'])->group(function (): void {
    Route::get('/map/characters', function (MapController $controller) {
        abort_unless((bool) Setting::get('map_frontend_enabled', false), 404);
        return $controller->characters();
    })->name('api.map.characters');
});

Route::middleware(['web', 'auth', 'verified', FilamentAdminMiddleware::class])->group(function (): void {
    Route::get('/admin/map/characters', [MapController::class, 'adminCharacters'])->name('api.admin.map.characters');
});
