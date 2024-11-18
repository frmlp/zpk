<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// w tym pliku znajdują się ścieżki do poszczególnych stron aplikacji

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

Route::get('/spacer', function () {
    return view('spacer');
});

// ten endpoint niedługo nie będzie potrzebny, rozwiązanie tymczasowe
// jak moje pliki html podmienie na templatki blade'a to token będzie wszysty w meta dane strony
Route::get('/token', function() {
    return response()->json(['token' => csrf_token()]);
});


// wyeksportowane endpointy związane z konkretną funkcjonalnością aplikacji
require __DIR__.'/admin.php';

require __DIR__.'/auth.php';