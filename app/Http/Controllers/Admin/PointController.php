<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function Laravel\Prompts\error;

class PointController extends Controller
{
    
    // private function validatePointData(Request $request)
    // {
    //     return $request->validate([
    //         'code' => 'required|string',
    //         'description' => 'required|string',
    //         'easting' => 'required|numeric',
    //         'northing' => 'required|numeric',
    //     ]);
    // }

    public function store(Request $request, Point $point)
    {   
        // logika endpointu STORE api/admin/points/{point}
        $validatedData = $request->validate(Point::rules());
        $point->create($validatedData); 

        return redirect()->route('admin.zpk')->with('success', 'Dodano nowy punkt');
    }

    public function index()
    {
        $points = Point::all();
        return response()->json($points);
    }

    public function destroy(Point $point)
    {
        // logika endpointu DELETE api/admin/points/{point}

        try {
            $point->findOrFail($point->id); 
            $point->delete();
            return redirect()->route("admin.zpk")->with('success', 'Usunięto');
        } catch (ModelNotFoundException $exception) {
            return redirect()->route("admin.zpk")->with('error', 'Nie znaleziono punktu.');
        }
    }

    public function update(Request $request, Point $point)
    {
        // logika endpointu PUT api/admin/points/{point}
        try {
            $point->findOrFail($point->id);
            $validatedData = $request->validate(Point::rules());
            $point->update($validatedData);
    
            return redirect()->route('admin.zpk')->with('success', 'Zaktualizowano punkt');
        } 
        catch (ModelNotFoundException $exception) {
            return redirect()->route('admin.zpk')->with('error', 'Nie znaleziono punktu.');
        }
    }
}        


