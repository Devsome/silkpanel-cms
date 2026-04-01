<?php

use App\Http\Controllers\DonationController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Donation frontend routes (auth required)
Route::middleware(['auth', 'verified'])->prefix('dashboard/donate')->group(function () {
    Route::get('/', [DonationController::class, 'index'])->name('donate.index');
    Route::get('/success', [DonationController::class, 'success'])->name('donate.success');
    Route::get('/cancel', [DonationController::class, 'cancel'])->name('donate.cancel');
    Route::get('/redeem-epin/{provider}', [DonationController::class, 'showRedeemEpin'])->name('donate.redeem-epin.show');
    Route::post('/redeem-epin/{provider}', [DonationController::class, 'redeemEpin'])->name('donate.redeem-epin');
    Route::get('/{provider}', [DonationController::class, 'packages'])->name('donate.packages');
    Route::post('/{package}/checkout', [DonationController::class, 'checkout'])->name('donate.checkout');
});

// Payment webhooks (CSRF excluded via bootstrap/app.php)
Route::prefix('webhook')->group(function () {
    Route::post('/paypal', [WebhookController::class, 'handlePayPal'])->name('webhook.paypal');
    Route::post('/stripe', [WebhookController::class, 'handleStripe'])->name('webhook.stripe');
    Route::post('/hipopay', [WebhookController::class, 'handleHipoPay'])->name('webhook.hipopay');
    Route::post('/fawaterk', [WebhookController::class, 'handleFawaterk'])->name('webhook.fawaterk');
});
