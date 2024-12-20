<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate(Area::rules());
            $area = Area::create($validatedData);
            
            return redirect()->route('admin.zpk')->with('success', 'Dodano nowy obszar'); 

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Błąd walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function index()
    {
        $points = Area::all();
        return response()->json($points, 200);
    }

    public function show(Area $area)
    {
        return response()->json($area, 200); 
    }

    public function destroy(Area $area)
    {
        try {
            $area->findOrFail($area->id); 
    
            // Pobranie wszystkich punktów powiązanych z obszarem
            $points = $area->points;
    
            // Usunięcie powiązań z obszarem w tablicy pośredniej
            foreach ($points as $point) {
                $point->areas()->detach($area->id);
            }
    
            $area->delete();
    
            return redirect()->route("admin.zpk")->with('success', 'Usunięto tag');
    
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono tagu.', 
            ], 404); 
        }
        return redirect()->route("admin.zpk")->with('success', 'Usunięto tag');
    }

}