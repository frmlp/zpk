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

            // usuwanie powielonych tagów
            $tagIds = $request->input('tag_ids');
            $uniqueTagIds = array_unique($tagIds);            

            $point = Point::create($validatedData); 

            $point->area_id = $request->input('area_id');
            $point->save();
            $point->pointTags()->attach($uniqueTagIds);

            return redirect()->route('admin.zpk')->with('success', 'Dodano nowy punkt');

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Blad walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function index()
    {   // logika endpointu GET api/admin/points
        $points = Point::with('pointTags')->get();
        return PointResource::collection($points)->response()->setStatusCode(200);
    }

    public function show(Point $point)
    {   // logika endpointu GET api/admin/points/{point}
        try {
            $point->load('pointTags'); 
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
            $point->findOrFail($point->id);
            // dodatkowa walidacja 
            $request->validate([
                'code' => [
                    Rule::unique('points')->ignore($point->id),
                ],
            ]);
            $validatedData = $request->validate(Point::rules());
            $point->update($validatedData);

            $point->area_id = $request->input('area_id');
            $point->save();

            $tagIds = $request->input('tag_ids');
            $uniqueTagIds = array_unique($tagIds);
            
            $point->pointTags()->sync($uniqueTagIds);

            return redirect()->route('admin.zpk')->with('success', 'Zaktualizowano punkt');

        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono punktu.',
            ], 404);
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
            $point->findOrFail($point->id);
            // odpięcie z tablicy pośredniej point_tags
            $point->pointTags()->detach(); 
            
            // odpięcie z tablicy pośredniej dla paths_points
            $paths = $point->paths()->get();
            foreach ($paths as $path) {
                $path->points()->detach($point->id); 
            }
            
            $point->delete();
            
            return response()->noContent();
    
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono punktu.',
            ], 404);
        }
    }
}
