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

});

Route::group(['middleware' => 'auth'], function () { // endpointy dla zalogowanych użytkowników
    
    Route::post('/logout', [AuthController2::class, 'logout'])->name('logout'); // wylogowanie z konta
    
    // Zarządzanie kontem:
    Route::post('/register', [AuthController2::class, 'registerPost'])->name('register.post');
    Route::delete('/users/{user}', [AuthController2::class, 'destroy'])->name('users.destroy');
    Route::put('/password/change', [AuthController2::class, 'updatePassword'])->name('password.update');
    
});