<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class TagController extends Controller
{

    public function store(Request $request, Tag $tag)
    {
        try{
            $validatedData = $request->validate(Tag::rules());
            // dodatkowa walidacja
            $request->validate([
                'tag' => [
                    Rule::unique('point_tags'), 
                ],
            ]);

            $tag = Tag::create($validatedData);
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
        $points = Tag::all();
        return response()->json($points, 200);
    }

    public function show(Tag $point_tag)
    {
        return response()->json($point_tag, 200); 
    }

    public function update(Request $request, Tag $point_tag)
    {
        // logika endpointu PUT api/admin/tags/{point_tag}
        try {
            $point_tag->findOrFail($point_tag->id);
            // dodatkowa walidacja przy dodawaniu tagów
            $request->validate([
                'tag' => [
                    Rule::unique('point_tags')->ignore($point_tag->id),
                ],
            ]);
            $validatedData = $request->validate(Tag::rules());
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

    public function destroy(Tag $point_tag)
    {
        try {
            $point_tag->findOrFail($point_tag->id); 
    
            // Pobranie wszystkich punktów powiązanych z tagiem
            $points = $point_tag->points;
    
            // Usunięcie powiązań z tagiem w tablicy pośredniej
            foreach ($points as $point) {
                $point->tags()->detach($point_tag->id);
            }
    
            $point_tag->delete();
    
            return redirect()->route("admin.zpk")->with('success', 'Usunięto tag');
    
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono tagu.', 
            ], 404); 
        }
        return redirect()->route("admin.zpk")->with('success', 'Usunięto tag');
    }
    
}
