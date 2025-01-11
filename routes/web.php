<?php

use Illuminate\Support\Facades\Route;


// w tym pliku znajdują się ścieżki do poszczególnych stron aplikacji

Route::get('/', function () {
    return view('pages.client.index');
});

Route::get('/baza', function () { 
    return view('pages.client.baza-tras'); 
})->name("baza-tras");

Route::get('/generator', function () {
    return view('pages.client.generator');
})->name("generator");

Route::get('/planner', function () {
    return view('pages.client.planer');
})->name("planer");

Route::get('/spacer', function () {
    return view('pages.client.spacer-vr');
})->name("spacer");

// ten endpoint niedługo nie będzie potrzebny, rozwiązanie tymczasowe
// jak moje pliki html podmienie na templatki blade'a to token będzie wszysty w meta dane strony
Route::get('/token', function() {
    return response()->json(['token' => csrf_token()]);
});


// wyeksportowane endpointy związane z konkretną funkcjonalnością aplikacji
require __DIR__.'/admin.php';

require __DIR__.'/auth.php';