<?php

use App\Http\Controllers\Admin\PointController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function() { // utworzenie grupy endpointów z prefixem '/admin/'
    

    //Route::middleware('auth')->group(function () { // endpointy w tej grupie wymagają żeby użytkownik był zalogowany

        // endpoint '/admin/zpk' - do przeniesienia w routing views
        
        Route::get('zpk', function() {
            return view('admin.zpk');
        })->name('admin.zpk');
        
        Route::post('points', [PointController::class, "store"])->name("admin.points.store");
        Route::get('points', [PointController::class, 'index'])->name('admin.points.index');
        Route::put('points/{point}', [PointController::class, "update"])->name("admin.points.update ");
        Route::delete('points/{point}', [PointController::class, "destroy"])->name("admin.points.destroy");

    /*ggh   
        
        Route::get('points/{mapId}', [PointController::class, "getPointsByMapId"])->name("admin.points.list");  // odpowiednik "index", usunięte z apiResource
        
        Route::get('points/{pointId}', [PointController::class, "show"])->name("admin.point.info"); // odpowiednik show, usunięte z apiResource
        // ^ implementacja GET /admin/docelowa-nazwa-zasobu
        // narazie frontend korzysta z endpointu zdefiniowanego w pliku 'api.php' co nie jest najlepszym rozwiązaniem
        
        
        
        // POST /admin/points
        
        Route::delete('points/{point}', [PointController::class, "destroy"])->name("admin.points.destroy");
        //ggh 
        // PFtodo po stronie front:
        // @method("DELETE")
        // DELETE /admin/points/{point}
        
        Route::update('points/{point}', [PointController::class, "update"])->name("admin.points.update");
        // PUT|Patch /admin/points/{point}
        
    */
            
        
        // do zaimplementowania:
        // 1. utworzenie endpointów RESTfull dla zasobu punktów wirtualnych podobnie jak dla punktów kontrolnych powyżej; rozważyć zmiany nazewnictwa 'control_points' i 'virtual_points'???
            // stan Virtual_point zaszyty jest w tabeli Points, podobnie jak ID_map ("Obszar" w admin/zpk) 

        // 2. utworzenie endpointów RESTfull dla zasobu tagów i tras;
            // jak ma to dokładnie wyglądać? to ma być zamknięta lista którą i tak sam admin tworzy?
            
        // 3. Czy implementujemy zmianę jakiś ustawień ??? czyłość punktów wirtualnych ???
            // na razie bym to olał

        // 4. logika zmiany loginu i hasła

  //  });
});


