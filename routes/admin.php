<?php

use App\Http\Controllers\Admin\PointController;
use App\Http\Controllers\Admin\PointTagController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function() { // utworzenie grupy endpointów z prefixem '/admin/'


//     Route::middleware('auth')->group(function () { // endpointy w tej grupie wymagają żeby użytkownik był zalogowany

        // endpoint '/admin/zpk' - do przeniesienia w routing views
        Route::get('zpk', function() {
            return view('admin.zpk');
        })->name('admin.zpk');


        // definicja endpointów RESTfull dla zasobu points
        // Route::apiResource('points', PointController::class) ->scoped();     // fragment PF

        Route::prefix('points')->controller(PointController::class)->name('admin.points')->group(function(){
            
            // create
            Route::post('/', 'store')->name('store');

            // read
            // ggh todo: przekierowanie indexu na endpoint po stronie klienta
            Route::get('/', 'index')->name('index');
            Route::get('/{point}', 'show')->name('show');
            
            // update
            Route::put('/{point}', 'update')->name('update');
            
            //delete
            Route::delete('/{point}', 'destroy')->name('destroy');



            Route::prefix('{point}/tags')->name('tags.')->controller(PointTagController::class)->group(function () {
                Route::post('/', 'store')->name('store');               // Create  // admin/point/tag
                Route::get('/', 'index')->name('index');                // Read
                Route::put('/{tag}', 'update')->name('update');         // Update
                Route::delete('/{tag}', 'destroy')->name('destroy');    // Delete
                
            });
        });


        // obszary do wbudowania w bazę


        // TODO:
        // 4. logika zmiany loginu i hasła





    {   // do zaimplementowania:

        // 1. utworzenie endpointów RESTfull dla zasobu punktów wirtualnych podobnie jak dla punktów kontrolnych powyżej; rozważyć zmiany nazewnictwa 'control_points' i 'virtual_points'???
            // stan Virtual_point zaszyty jest w tabeli Points, podobnie jak ID_map ("Obszar" w admin/zpk)

        // 2. utworzenie endpointów RESTfull dla zasobu tagów i tras;
            // jak ma to dokładnie wyglądać? to ma być zamknięta lista którą i tak sam admin tworzy?

        // 3. Czy implementujemy zmianę jakiś ustawień ??? czyłość punktów wirtualnych ???
            // na razie bym to olał

        // 4. logika zmiany loginu i hasła
    }

//    });
});


