<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\PointTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class PointTagController extends Controller
{

    public function store(Request $request, PointTag $tag)
    {
        try{
            $validatedData = $request->validate(PointTag::rules());
            // dodatkowa walidacja
            $request->validate([
                'tag' => [
                    Rule::unique('point_tags'), 
                ],
            ]);

            $tag = PointTag::create($validatedData);
            return redirect()->route('admin.zpk')->with('success', 'Dodano nowy tag do punktu');
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Blad walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }
    
    public function index()
    {
        $points = PointTag::all();
        return response()->json($points, 200);
    }

    public function show(PointTag $point_tag)
    {
        return response()->json($point_tag, 200); 
    }

    public function update(Request $request, PointTag $point_tag)
    {
        // logika endpointu PUT api/admin/pointTags/{point_tag}
        try {
            $point_tag->findOrFail($point_tag->id);
            // dodatkowa walidacja przy dodawaniu tagów
            $request->validate([
                'tag' => [
                    Rule::unique('point_tags')->ignore($point_tag->id),
                ],
            ]);
            $validatedData = $request->validate(PointTag::rules());
            $point_tag->update($validatedData);

            return redirect()->route('admin.zpk')->with('success', 'Zaktualizowano tag');

        }  catch (ValidationException $e) {
            Log::error('Blad walidacji podczas aktualizacji tagu:', $e->errors());

            return response()->json([
                'message' => 'Blad walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(PointTag $point_tag)
    {
        try {
            $point_tag->findOrFail($point_tag->id); 
    
            // Pobranie wszystkich punktów powiązanych z tagiem
            $points = $point_tag->points;
    
            // Usunięcie powiązań z tagiem w tablicy pośredniej
            foreach ($points as $point) {
                $point->pointTags()->detach($point_tag->id);
            }
    
            $point_tag->delete();
    
            return redirect()->route("admin.zpk")->with('success', 'Usunięto tag');
    
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono tagu.', 
            ], 404); 
        }
        // $point_tag->delete();
        // ggh todo: usunięcie rekordów z tablicy pośredniej points_pointTags
        return redirect()->route("admin.zpk")->with('success', 'Usunięto tag');
    }
    
}
