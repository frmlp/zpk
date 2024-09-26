<?php

use App\Http\Controllers\AuthController2;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route::middleware('guest')->group(function() {
//     Route::get('login', [AuthenticatedSessionController::class, 'create'])
//                 ->name('login');
    
//     Route::post('login', [AuthenticatedSessionController::class, 'store']);
// });

Route::group(['middleware' => 'guest'], function() {
    
    Route::get('/login', [AuthController2::class, 'login'])->name('login');
    Route::post('/login', [AuthController2::class, 'loginPost'])->name('login');
});

Route::group(['middleware' => 'auth'], function () {
    
    Route::post('/logout', [AuthController2::class, 'logout'])->name('logout');
});