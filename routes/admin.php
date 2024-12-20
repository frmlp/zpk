<?php

use App\Http\Controllers\Admin\PointController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\PathController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function() { // utworzenie grupy endpointów z prefixem '/admin/'

    Route::middleware('auth')->group(function () { // endpointy w tej grupie wymagają żeby użytkownik był zalogowany

        // endpoint '/admin/zpk' - do przeniesienia w routing views
        Route::get('zpk', function() {
            return view('admin.zpk');
        })->name('admin.zpk');

        //  POINTS
        Route::prefix('points')->controller(PointController::class)->name('admin.points')->group(function(){

            Route::post('/', 'store')->name('store');
            Route::get('/', 'index')->name('index');        // ggh todo: przekierowanie indexu na endpoint po stronie klienta
            Route::get('/{point}', 'show')->name('show');   // ggh todo: przekierowanie indexu na endpoint po stronie klienta
            Route::put('/{point}', 'update')->name('update');
            Route::delete('/{point}', 'destroy')->name('destroy');            
        });

        // TAGS
        Route::prefix('tags')->controller(tagController::class)->name('admin.tags')->group(function () {
            
            Route::post('/', 'store')->name('store');
            Route::get('/', 'index')->name('index');
            Route::get('/{tag}', 'show')->name('show');
            Route::put('/{tag}', 'update')->name('update');
            Route::delete('/{tag}', 'destroy')->name('destroy');
        });

        // AREAS
        Route::prefix('areas')->controller(AreaController::class)->name('admin.areas')->group(function () {
    
            Route::post('/', 'store')->name('store');
            Route::get('/', 'index')->name('index');
            Route::get('/{area}', 'show')->name('show');
            // Route::put('/{area}', 'update')->name('update');
            Route::delete('/{area}', 'destroy')->name('destroy');  // do poprawy
        });

        //  PATHS
        Route::prefix('paths')->controller(PathController::class)->name('admin.paths')->group(function(){

            Route::post('/', 'store')->name('store');
            Route::get('/', 'index')->name('index');        // ggh todo: przekierowanie indexu na endpoint po stronie klienta
            Route::get('/{path}', 'show')->name('show');    // ggh todo: przekierowanie indexu na endpoint po stronie klienta
            Route::put('/{path}', 'update')->name('update');
            Route::delete('/{path}', 'destroy')->name('destroy');            
        });
        
        // obszary do wbudowania w bazę


        // ggh TODO:
        // 4. logika zmiany loginu i hasła


    {   // do zaimplementowania:
        // 3. Czy implementujemy zmianę jakiś ustawień ??? czyłość punktów wirtualnych ???
            // na razie bym to olał

    }

   });
});


