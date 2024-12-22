<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
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
    
            return response()->json([
                'message' => 'Dodano nowy obszar',
                'area' => new AreaResource($area) 
            ], 201);
            // return response()->json([
            //     'message' => 'Dodano nowy obszar'
            // ], 201);
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Błąd walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function index()
    {
        $areas = Area::all();
        return AreaResource::collection($areas);
    }

    public function show(Area $area)
    {
        return new AreaResource($area);
        
    }

    public function update(Request $request, Area $area)
    {
        try {
            $validatedData = $request->validate(Area::rules());
            $area->update($validatedData);

            return response()->json([
                'message' => 'Zaktualizowano obszar',
                'area' => new AreaResource($area)
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Błąd walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(Area $area)
    {
        try {
            // Usunięcie powiązań z obszarem w tablicy pośredniej area_point
            $area->points()->detach(); 
            $area->delete();

            return response()->json([
                'message' => 'Obszar został usunięty' 
            ], 200);

        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono obszaru.',
            ], 404);
        }
    }
}