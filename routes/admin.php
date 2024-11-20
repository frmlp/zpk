<?php

use App\Http\Controllers\Admin\PointController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function() { // utworzenie grupy endpointów z prefixem '/admin/'
    

    Route::middleware('auth')->group(function () { // endpointy w tej grupie wymagają żeby użytkownik był zalogowany

        // endpoint '/admin/zpk'
        Route::get('/zpk', function() {
            return view('admin.zpk');
        })->name('admin.zpk');

        // definicja endpointów RESTfull dla zasobu points
        // utworzenie endpointów:
        // POST /admin/points
        // PUT|Patch /admin/points/{point}
        // DELETE /admin/points/{point}
        Route::apiResource('points', PointController::class) 
            ->scoped()->except(['index', 'show']);
            // przyda się implementacja GET /admin/docelowa-nazwa-zasobu
            // narazie frontend korzysta z endpointu zdefiniowanego w pliku 'api.php' co nie jest najlepszym rozwiązaniem

        
        // do zaimplementowania:
        // 1. utworzenie endpointów RESTfull dla zasobu punktów wirtualnych podobnie jak dla punktów kontrolnych powyżej; rozważyć zmiany nazewnictwa 'control_points' i 'virtual_points'???
        // 2. utworzenie endpointów RESTfull dla zasobu tagów;
        // 3. Czy implementujemy zmianę jakiś ustawień ??? czyłość punktów wirtualnych? włączanie/wyłączanie taowania???

    });
});


