<?php

use App\Http\Controllers\GeneratorServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MapDownloadController;
use App\Http\Resources\PathResource;
use App\Models\Path;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/paths', function() {
    return PathResource::collection(Path::all());
});

Route::get('/map-download', MapDownloadController::class);

Route::get('/generator', GeneratorServer::class);
