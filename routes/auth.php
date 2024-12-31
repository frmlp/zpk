<?php

use App\Http\Controllers\AuthController2;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;


// w tym pliku definiujemy funcjonalności autoryzacji, uwierzytelniania i zarządzania kontem admina
// w naszym przypadku tutaj będzie tylko logika 

Route::group(['middleware' => 'guest'], function() { // endpointy dla niezalogowanych użytkowników
    
    Route::get('/login', [AuthController2::class, 'login'])->name('login'); // przekierowanie na stronę logowania
    Route::post('/login', [AuthController2::class, 'loginPost'])->name('login'); // wysłanie podanych danych logowania na serwer w celu autoryzacji    
    
    // do zaimplementowania zmiana loginu i hasła do konta admina:
    
    // rejestracja nowych użytkowników
    // Route::get('/register', [AuthController2::class, 'register'])->name('register');
    Route::post('/register', [AuthController2::class, 'registerPost'])->name('register.post');

    // edycja istniejących użytkowników
    Route::get('/profile', [AuthController2::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController2::class, 'profileUpdate'])->name('profile.update');

    // zmiana hasła
    Route::get('/password/change', [AuthController2::class, 'changePassword'])->name('password.change');
    Route::put('/password/change', [AuthController2::class, 'updatePassword'])->name('password.update');

});

Route::group(['middleware' => 'auth'], function () { // endpointy dla zalogowanych użytkowników
    
    Route::post('/logout', [AuthController2::class, 'logout'])->name('logout'); // wylogowanie z konta

   

});