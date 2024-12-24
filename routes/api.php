<?php

use App\Http\Controllers\GeneratorService;
use App\Http\Controllers\MapDownloadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ggh do sprawdzenia: ten endpoint jest generowany automatycznie, chyba niepotrzebny
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// POINTS 'api/points'
Route::get('/points', function () {
    return redirect()->route('admin.points.index');
});

Route::get('/points/{point}', function ($point) {
    return redirect()->route('admin.points.show', ['point' => $point]);
});


// PATHS 'api/paths'
Route::get('/paths', function() {
    return redirect()->route('admin.paths.index');
    // return PathResource::collection(Path::all());
});

Route::get('/paths/{path}', function ($path) {
    return redirect()->route('admin.paths.show', ['path' => $path]);
});

// endpoint przez który wysyła się zapytanie z parametrami do generatora
// jako odpowiedź zwrotna dostaje się wygenerowane trasy
Route::get('/generator', GeneratorService::class);


// MAPS
// endpoint przez który pobiera się czystą mapę podkładową 'api/map-download'
Route::get('/map-download', MapDownloadController::class);


// ggh todo:
// implementacja endpointu, który na podstawie przesłanego kodu zapisuje trasę w bazie danych
