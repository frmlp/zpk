<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PointController;
use Illuminate\Support\Facades\Route;

// Route::middleware('guest')->group(function() {
//     Route::get('/login', function() {
//         return view('admin.login');
//     })->name('login');

//     Route::post('/login', [AuthController::class, 'authenticate']);

// });

// Route::prefix('admin')->group(function() {
    
//     Route::middleware('auth')->group(function() {
//         Route::get('home', function() {
//             return view('admin.dashboard');
//         });

//         Route::get('/zpk', function() {
//             return view('admin.zpk');
//         });
//     });

//     Route::get('csrf-token', function() {
//         error_log('csrf');
//         return response()->json(['token' => csrf_token()]);
//     });

    
//     Route::apiResource('points', PointController::class)
//         ->scoped()->except(['index', 'show']);
// });
Route::prefix('admin')->group(function() {
    

    Route::middleware('auth')->group(function () {
        Route::get('/zpk', function() {
            return view('admin.zpk');
        })->name('admin.zpk');

        Route::apiResource('points', PointController::class)
            ->scoped()->except(['index', 'show']);

        // Route::get('csrf-token', function() {
        //     error_log('csrf');
        //     return response()->json(['token' => csrf_token()]);
        // });

    });
});


