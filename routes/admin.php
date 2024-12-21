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
                // ggh todo: post, put: obsługa dubli area_id i nieistniejącego area_id, 
                // ggh todo: delete : aktualizacja position w path_point
                // ggh ask: czy punkt ma się wstawić w przypadku błędnej walidacji tagów czy area?
        });

        // TAGS
        Route::prefix('tags')->controller(tagController::class)->name('admin.tags')->group(function () {
            
            Route::post('/', 'store')->name('store');
            Route::get('/', 'index')->name('index');
            Route::get('/{tag}', 'show')->name('show');
            Route::put('/{tag}', 'update')->name('update');
            Route::delete('/{tag}', 'destroy')->name('destroy');
                // ggh todo: store: zwrot dodanego tagu w json
        });

        // AREAS
        Route::prefix('areas')->controller(AreaController::class)->name('admin.areas')->group(function () {
    
            Route::post('/', 'store')->name('store');
            Route::get('/', 'index')->name('index');
            Route::get('/{area}', 'show')->name('show');
            Route::put('/{area}', 'update')->name('update');
            Route::delete('/{area}', 'destroy')->name('destroy');  // do poprawy
                // ggh ask: czy nazwy muszą być unikatowe? jeżeli do danego obszaru będziemy mieli różne podkłady?

        });

        //  PATHS
        Route::prefix('paths')->controller(PathController::class)->name('admin.paths')->group(function(){

            Route::post('/', 'store')->name('store');
            Route::get('/', 'index')->name('index');        // ggh todo: przekierowanie indexu na endpoint po stronie klienta
            Route::get('/{path}', 'show')->name('show');    // ggh todo: przekierowanie indexu na endpoint po stronie klienta
            Route::put('/{path}', 'update')->name('update');
            Route::delete('/{path}', 'destroy')->name('destroy');      
                // ggh todo: store, update: weryfikacja istnienia dodawanych punktów
                // ggh todo: update: odpina path_id z path_point w przypadku braku point
                // ggh ask: czy path ma się utworzyć przy błędnej walidacji points? 
                // ggh ask: czy mamy zwracać tworzoną lub aktualizowaną ścieżkę w json?  
        });
        
        


        // ggh TODO:
        // 4. logika zmiany loginu i hasła


    {   // do zaimplementowania:
        // 3. Czy implementujemy zmianę jakiś ustawień ??? czyłość punktów wirtualnych ???

    }

   });
});


