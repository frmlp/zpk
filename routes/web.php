<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/baza', function () {
    return view('baza-tras');
});

Route::get('/generator', function () {
    return view('generator');
});

Route::get('/planner', function () {
    return view('planner-tras');
});

// =========================

// Route::get('/login', function() {
//     return view('auth.login');
// });

// Route::post('/login', [AuthController::class, 'authenticate'])->middleware('auth');

// Route::middleware(['auth'])->group(function() {
//     Route::get('/main', function() {
//         return view('auth.main');
//     });
// });

Route::get('/token', function() {
    return response()->json(['token' => csrf_token()]);
});



// Route::get('/auth-main', function () {
//     return view('auth.main');
// })->middleware(['auth', 'verified'])->name('auth-main');


require __DIR__.'/admin.php';

require __DIR__.'/auth.php';