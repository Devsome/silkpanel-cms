<?php

use App\Helpers\SettingHelper;
use App\Models\Setting;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('index');

Route::get('/terms', function () {
    abort_unless((bool) Setting::get('tos_enabled', false), 404);

    return view('terms', [
        'tosText' => Setting::get('tos_text', ''),
    ]);
})->name('terms');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, SettingHelper::frontendLanguages(), true), 404);

    session(['frontend_locale' => $locale]);

    return redirect()->back();
})->name('language.switch');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/donation.php';
require __DIR__ . '/installer.php';
