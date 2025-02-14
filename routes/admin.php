<?php

use App\Http\Controllers\Admin\PointController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\PathController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function() { // utworzenie grupy endpointów z prefixem '/admin/'

    // PUBLIC routes:
    Route::get('/points', [PointController::class, 'index'])->name('admin.points.index');
    Route::get('/points/{point}', [PointController::class, 'show'])->name('admin.points.show');
    Route::get('/paths', [PathController::class, 'index'])->name('admin.paths.index');
    Route::get('/paths/non-virtual', [PathController::class, 'nonVirtualPaths'])->name('admin.nonVirtualPaths');
    Route::get('/paths/{path}', [PathController::class, 'show'])->name('admin.paths.show');
    Route::get('/tags', [TagController::class, 'index'])->name('admin.tags.index');
    Route::get('/areas', [AreaController::class, 'index'])->name('admin.areas.index');    

    // PRIVATE routes:
    Route::middleware('auth')->group(function () { // endpointy w tej grupie wymagają żeby użytkownik był zalogowany
        Route::get('baza-tras', function() {
            return view('pages.admin.baza-tras');
        })->name('admin.baza-tras');

        // endpoint '/admin/zpk' - do przeniesienia w routing views
        Route::get('zpk', function() {
            return view('pages.admin.punkty-kontrolne');
        })->name('admin.zpk');

        Route::get('tagi', function() {
            return view('pages.admin.tagi');
        })->name('admin.tagi');

        Route::get('ustawienia', function() {
            return view('pages.admin.ustawienia');
        })->name('admin.ustawienia');


        //  POINTS
        Route::prefix('points')->controller(PointController::class)->name('admin.points.')->group(function(){

            Route::post('/', 'store')->name('store');
            Route::put('/{point}', 'update')->name('update');
            Route::delete('/{point}', 'destroy')->name('destroy');

        });

        //  PATHS
        Route::prefix('paths')->controller(PathController::class)->name('admin.paths.')->group(function(){

            Route::post('/', 'store')->name('store');
            Route::put('/{path}', 'update')->name('update');
            Route::delete('/{path}', 'destroy')->name('destroy');      

        });

        // TAGS
        Route::prefix('tags')->controller(tagController::class)->name('admin.tags.')->group(function () {
            
            Route::post('/', 'store')->name('store');
            Route::get('/{tag}', 'show')->name('show');
            Route::put('/{tag}', 'update')->name('update');
            Route::delete('/{tag}', 'destroy')->name('destroy');

        });

        // AREAS
        Route::prefix('areas')->controller(AreaController::class)->name('admin.areas.')->group(function () {
    
            Route::post('/', 'store')->name('store');
            Route::get('/{area}', 'show')->name('show');
            Route::put('/{area}', 'update')->name('update');
            Route::delete('/{area}', 'destroy')->name('destroy');  

        });
    });
});


