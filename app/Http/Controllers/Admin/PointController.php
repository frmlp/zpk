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
            $point->assignAreas(); 
            { // poprzednia wersja przypisania do tabeli pośredniej
                // $areaIds = $request->input('area_ids', []);
                // $point->areas()->attach($areaIds); 
            }
            
            // Sprawdzenie okolicy (sector)
            $sectorMessage = $this->isPointInSameSector($point, $request)
            ? 'Uwaga: W okolicy znajduje się już inny punkt.'
            : 'W okolicy nie znajdował się jeszcze żaden punkt.';

            return response()->json([
                'message' => 'Dodano nowy punkt',
                'sector_message' => $sectorMessage,
                'point' => new PointResource($point) 
            ], 201); 

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

            $originalEasting = $point->easting;
            $originalNorthing = $point->northing;

            $point->update($validatedData);

            if ($originalEasting != $point->easting || $originalNorthing != $point->northing) {
                $point->areas()->detach(); // Odłącz wszystkie obszary
                $point->assignAreas();     // Przypisz na nowo
            }
            { // poprzednia wersja
                // $areaId = $request->input('area_id');
                // $point->areas()->sync($areaId);
            }

            $tagIds = $request->input('tag_ids', []); 
            $uniqueTagIds = array_unique($tagIds);
            $point->tags()->sync($uniqueTagIds);

            // Sprawdzenie okolicy (sector)
            $sectorMessage = $this->isPointInSameSector($point, $request)
            ? 'Uwaga: W okolicy znajduje się już inny punkt.'
            : 'W okolicy nie znajdował się jeszcze żaden punkt.';

            return response()->json([
                'message' => 'Zaktualizowano punkt',
                'sector_message' => $sectorMessage,
                'point' => new PointResource($point) 
            ], 201); 

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
            $point->areas()->detach(); 
       
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

   
    // METODY POMOCNICZE:
    private function isPointInSameSector(Point $point)
    {
        $minDistance = config('app.sector_range', 100);

        $easting = $point->easting;
        $northing = $point->northing;

        // Sprawdź sektor dla istniejących punktów, z wyłączeniem aktualizowanego punktu
        return Point::where('id', '!=', $point->id)
                    ->whereBetween('easting', [$easting - $minDistance, $easting + $minDistance])
                    ->whereBetween('northing', [$northing - $minDistance, $northing + $minDistance])
                    ->exists();
    }

}
