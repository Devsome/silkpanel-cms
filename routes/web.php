<?php

use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Template management routes
Route::prefix('templates')->name('templates.')->group(function () {
    Route::get('/', [TemplateController::class, 'index'])->name('index');
    Route::get('/create', [TemplateController::class, 'create'])->name('create');
    Route::post('/', [TemplateController::class, 'store'])->name('store');
    Route::post('/{template}/activate', [TemplateController::class, 'activate'])->name('activate');
    Route::post('/deactivate', [TemplateController::class, 'deactivate'])->name('deactivate');
    Route::delete('/{template}', [TemplateController::class, 'destroy'])->name('destroy');
});
