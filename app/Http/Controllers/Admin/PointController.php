<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PointResource;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;


class PointController extends Controller
{
    public function store(Request $request, Point $point)
    {   // logika endpointu POST api/admin/points/
        try {
            $validatedData = $request->validate(Point::rules());
            // dodatkowa walidacja
            $request->validate([
                'code' => [
                    Rule::unique('points'), 
                ],
            ]);

            // Tworzenie punktu
            $point = new Point($validatedData); 
            $point->save();

            // Przypisywanie tagów
            $tagIds = $request->input('tag_ids', []);
            $uniqueTagIds = array_unique($tagIds);
            $point->tags()->attach($uniqueTagIds);

            // Przypisywanie obszarów
            $areaIds = $request->input('area_ids', []);
            $point->areas()->attach($areaIds); 

            return response()->json(['message' => 'Dodano nowy punkt'], 201); 

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Blad walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function index()
    {   // logika endpointu GET api/admin/points
        $points = Point::with('tags', 'areas')->get();
        return PointResource::collection($points)->response()->setStatusCode(200);
    }

    public function show(Point $point)
    {   // logika endpointu GET api/admin/points/{point}
        try {
            $point->load('tags'); 
            return (new PointResource($point))->response()->setStatusCode(200); 
    
        } catch (\Exception $exception) {
            Log::error($exception); 

            return response()->json([
                'message' => 'Wystąpił błąd serwera.', 
            ], 500);
        }
    }
    
    public function update(Request $request, Point $point)
    {   // logika endpointu PUT api/admin/points/{point}
        try {
            $validatedData = $request->validate(Point::rules());
            $request->validate([
                'code' => [
                    Rule::unique('points')->ignore($point->id),
                ],
            ]);
            $point->update($validatedData);

            $areaId = $request->input('area_id');
            $point->areas()->sync($areaId);

            $tagIds = $request->input('tag_ids', []); 
            $uniqueTagIds = array_unique($tagIds);
            $point->tags()->sync($uniqueTagIds);

            return response()->json([
                'message' => 'Zaktualizowano punkt',
                'point' => $point, 
            ], 200); 

        } catch (ValidationException $e) {
            Log::error('Bledy walidacji podczas aktualizacji punktu:', $e->errors());

            return response()->json([
                'message' => 'Blad walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(Point $point)
    {   // logika endpointu DELETE api/admin/points/{point}
        
        try {
            $point->tags()->detach(); 
            $point->paths()->detach(); 
       
            $point->delete();

            return response()->json([
                'message' => 'Punkt został usunięty.'
              ], 200);   

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Nie znaleziono ścieżki',
            ], 404);
        }
    }
}
