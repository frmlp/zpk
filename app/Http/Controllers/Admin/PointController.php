<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PointController extends Controller
{

    // logika endpointu PUT|PATCH api/admin/points/{point}
    public function update(Request $request, Point $point)
    {
        Log::info($request);
        Log::info($point);

        error_log("PointController::update()");
        error_log($point);
        $point->update(
            $request->validate([
                'code' => 'required|string',
                'description' => 'required|string',
                'easting' => 'required|numeric',
                'northing' => 'required|numeric'
            ])
        );

        return redirect('/admin/zpk')->with('success', 'Zaktualizowano');
    }

    // logika endpointu POST api/admin/points

    public function store(Request $request, Point $point)
    {
        // Log::info($request);
        // $point = new Point();
        
        
        //ggh
        /*
        $point->code = $request->code;
        $point->description = $request->description;
        $point->easting = $request->easting;
        $point->northing = $request->northing;
        $point->ID_map = $request->ID_map;
        $point->Virtual_point = $request->Virtual_point;

        $point->save();

        return redirect()->route("admin.zpk")->with('success', 'Dodano');
        // czy potrzebne jest przekazanie points w tym miejscu?
        // return redirect()->route("admin.zpk")->with("points", Point::all());
    
        */



        //ggh ToDelete:
        
        // Gate::authorize('create', Point::class);

        $point = Point::create([
            ...$request->validate([
                'code' => 'required|string',
                'description' => 'required|string',
                'easting' => 'required|numeric',
                'northing' => 'required|numeric'
            ]),
        ]);
        
        return redirect('/admin/zpk')->with('success', 'Dodano');
        
    }



    // do zaimplementowania logika endpointu DELETE api/admin/points/{point}
    //ggh
    /*
    public function destroy(Request $request, Point $point)
    {


        return redirect()->route("admin.zpk")->with('success', 'Usunieto');
    }
    */ 

}
