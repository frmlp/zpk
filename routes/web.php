<?php

use Illuminate\Support\Facades\Route;


// w tym pliku znajdują się ścieżki do poszczególnych stron aplikacji

Route::get('/', function () {
    return view('index');
});

Route::get('/baza', function () { 
    return view('baza-tras'); 
})->name("baza-tras");

Route::get('/generator', function () {
    return view('generator');
})->name("generator");

Route::get('/planner', function () {
    return view('planner-tras');
})->name("planer");

Route::get('/spacer', function () {
    return view('spacer');
})->name("spacer");

Route::get('/token', function() {
    return response()->json(['token' => csrf_token()]);
});


// wyeksportowane endpointy związane z konkretną funkcjonalnością aplikacji
require __DIR__.'/admin.php';

require __DIR__.'/auth.php';