<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->dashboardRouteName());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('Pages.Admin.dashboard');
    })->name('admin.dashboard');
});

Route::middleware(['auth', 'verified', 'role:donatur'])->prefix('donatur')->group(function () {
    Route::get('/dashboard', function () {
        return view('Pages.Donatur.dashboard');
    })->name('donatur.dashboard');
});

Route::middleware(['auth', 'verified', 'role:user'])->prefix('user')->group(function () {
    Route::get('/dashboard', function () {
        return view('Pages.User.dashboard');
    })->name('user.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
