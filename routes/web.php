<?php

use App\Helpers\SettingHelper;
use App\Http\Controllers\Admin\SessionModalPreviewController;
use App\Http\Controllers\Api\SessionModalController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\VotingController;
use App\Models\Setting;
use App\Services\TemplateService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('template::welcome');
})->name('index');

Route::get('/templates/{slug}/preview-image', function (string $slug, TemplateService $templateService) {
    abort_unless(preg_match('/^[a-z0-9\-]+$/', $slug) === 1, 404);

    $path = $templateService->getPreviewImagePath($slug);
    abort_unless($path !== null, 404);

    return response()->file($path);
})->name('template.preview-image');

Route::get('/terms', function () {
    abort_unless((bool) Setting::get('tos_enabled', false), 404);

    return view('template::terms', [
        'tosText' => Setting::get('tos_text', ''),
    ]);
})->name('terms');

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, SettingHelper::frontendLanguages(), true), 404);

    session(['frontend_locale' => $locale]);

    return redirect()->back();
})->name('language.switch');

// Public content routes
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

// Rankings (public)
Route::prefix('ranking')->name('ranking.')->group(function () {
    Route::get('/characters', fn() => view('template::ranking.characters'))->name('characters');
    Route::get('/characters/{id}', [RankingController::class, 'showCharacter'])->name('characters.show')->where('id', '[0-9]+');
    Route::get('/guilds', fn() => view('template::ranking.guilds'))->name('guilds');
    Route::get('/guilds/{id}', [RankingController::class, 'showGuild'])->name('guilds.show')->where('id', '[0-9]+');
    Route::get('/uniques', fn() => view('template::ranking.uniques'))->name('uniques');
});

// Authenticated routes
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard/silk-history', [DashboardController::class, 'silkHistory'])->middleware(['auth', 'verified'])->name('dashboard.silk-history');
Route::get('/dashboard/map', function () {
    abort_unless((bool) Setting::get('map_frontend_enabled', false), 404);
    return view('template::dashboard.map');
})->middleware(['auth', 'verified'])->name('dashboard.map');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard/voting', [VotingController::class, 'index'])->name('voting.index');
    Route::get('/dashboard/webmall', function () {
        abort_unless((bool) Setting::get('webmall_enabled', false), 404);
        return view('template::webmall.index');
    })->name('webmall.index');
});

// Session modal dismissal (works for guests and logged-in users)
Route::post('/session-modals/dismiss', [SessionModalController::class, 'dismiss'])
    ->name('session-modals.dismiss');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Session modal preview (admin only)
    Route::get('/admin/session-modals/{modal}/preview', [SessionModalPreviewController::class, 'show'])
        ->middleware(\App\Http\Middleware\FilamentAdminMiddleware::class)
        ->name('admin.session-modals.preview');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/donation.php';
require __DIR__ . '/installer.php';
