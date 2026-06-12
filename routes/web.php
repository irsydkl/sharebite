<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DonaturController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Midtrans Webhook (no auth, no CSRF) ───────────────────────────────────
Route::post('/webhook/midtrans', [MidtransWebhookController::class, 'handle'])
    ->name('webhook.midtrans');

// Guest or Authenticated Redirect Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// General Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route(auth()->user()->dashboardRouteName());
    })->name('dashboard');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::patch('/profile/store', [ProfileController::class, 'updateStore'])
        ->middleware('role:donatur')
        ->name('profile.store.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dynamic Shared Routes (Role-based dispatch)
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('riwayat.index');
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifikasi.read');
});

// Donatur (Donor) Routes
Route::middleware(['auth', 'verified', 'role:donatur'])->prefix('donatur')->group(function () {
    Route::get('/dashboard', [DonaturController::class, 'index'])->name('donatur.dashboard');
    Route::get('/food/create', [DonaturController::class, 'create'])->name('donasi.create');
    Route::post('/food/store', [DonaturController::class, 'store'])->name('donasi.store');
    Route::get('/claims', [DonaturController::class, 'claims'])->name('donatur.claims');
    Route::post('/claims/{id}/status', [DonaturController::class, 'updateClaimStatus'])->name('donatur.claims.update');
    Route::get('/payouts', [DonaturController::class, 'payouts'])->name('donatur.payouts');
});

// User (Recipient) Routes
Route::middleware(['auth', 'verified', 'role:user'])->prefix('user')->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::get('/food/{id}', [UserController::class, 'showFood'])->name('user.food.show');
    Route::post('/food/{id}/claim', [UserController::class, 'claimFood'])->name('user.food.claim');
    Route::get('/claims/{id}/payment', [UserController::class, 'showPayment'])->name('user.claims.payment');
    Route::get('/claims/{id}/payment/return', [UserController::class, 'paymentReturn'])->name('user.claims.payment.return');
    Route::post('/claims/{id}/rate', [UserController::class, 'rateClaim'])->name('user.claims.rate');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/verifikasi/donatur', [AdminController::class, 'pendingDonaturs'])->name('admin.verifikasi.donatur');
    Route::post('/verifikasi/donatur/{id}', [AdminController::class, 'approveDonatur'])->name('admin.verifikasi.donatur.process');
    Route::get('/verifikasi/makanan', [AdminController::class, 'pendingFoods'])->name('admin.verifikasi.food');
    Route::post('/verifikasi/makanan/{id}', [AdminController::class, 'approveFood'])->name('admin.verifikasi.food.process');
    Route::get('/payouts', [AdminController::class, 'payouts'])->name('admin.payouts');
    Route::post('/payouts/{id}/process', [AdminController::class, 'processPayout'])->name('admin.payouts.process');
});

require __DIR__.'/auth.php';
