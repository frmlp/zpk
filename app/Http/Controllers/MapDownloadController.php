<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapDownloadController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $path = storage_path('app/public/mapa_org.pdf');
        return response()->download($path);
    }
}
