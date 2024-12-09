<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MapDownloadController;
use App\Http\Resources\GeneratorPointResource;
use App\Http\Resources\PathResource;
use App\Models\Path;
use App\Models\Point;

// w tym pliku znajdują się endpointy API, przez które pobiera się dane na frondend/mobile

// ten endpoint jest generowany automatycznie, chyba niepotrzebny

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// endpoint przez który pobiera się dane o trasach (BAZA TRAS) 'api/paths'
Route::get('/paths', function() {
    return PathResource::collection(Path::all());
});

// endpoint przez który pobiera się dane o wszystkich punktach (listy rozwijalne na froncie, podgląd punktów na mapie itp.)
Route::get('/points', function() {
    return GeneratorPointResource::collection(Point::all());
});

// endpoint przez który pobiera się czystą mapę podkładową 'api/map-download'
Route::get('/map-download', MapDownloadController::class);

// endpoint przez który wysyła się zapytanie z parametrami do generatora
// jako odpowiedź zwrotna dostaje się wygenerowane trasy
Route::get('/generator', GeneratorService::class);


// implementacja endpointu, który na podstawie przesłanego kodu zapisuje trasę w bazie danych

