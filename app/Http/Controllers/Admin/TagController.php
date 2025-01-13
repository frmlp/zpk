<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class TagController extends Controller
{

    public function store(Request $request)
    {   // logika endpointu POST api/admin/tags/
        try{
            $validatedData = $request->validate(Tag::rules());
            // dodatkowa walidacja
            $request->validate([
                'name' => [
                    Rule::unique('tags'), 
                ],
            ]);

            $tag = Tag::create($validatedData);
            
            return response()->json([
                'message' => 'Dodano nowy tag',
                'tag' => new TagResource($tag) 
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Błąd walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }
    
    public function index()
    {   // logika endpointu GET api/admin/tags/
        $tags = Tag::with('points')->get();

        return response()->json([
            'tags' => $tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'points' => $tag->points->pluck('code')->all(),
                ];
            }),
        ]);

    }

    public function show(Tag $tag)
    {   // logika endpointu POST api/admin/tags/{tag}
        $tag->load('points'); 

        return response()->json([
            'id' => $tag->id,
            'name' => $tag->name,
            'points' => $tag->points->pluck('code')->all(),
        ]);
    }

    public function update(Request $request, Tag $tag)
    {   // logika endpointu PUT api/admin/tags/{tag}
        
        try {
            $tag->findOrFail($tag->id);
            $request->validate([
                'name' => [
                    Rule::unique('tags')->ignore($tag->id),
                ],
            ]);
            $validatedData = $request->validate(Tag::rules());
            
            // Aktualizacja tagu który jest już w użyciu (posiada kolekcje punktów)
            if ($tag->points()->exists()) {
                return response()->json([
                    'message' => 'Nie można zaktualizować tagu, ponieważ jest on w użyciu.',
                    'points' => $tag->points->pluck('code')->all(),
                ], 400);
            }

            $tag->update($validatedData);

            return response()->json([
                'message' => 'Zaktualizowano tag',
                'tag' => new TagResource($tag), 
            ], 200); 

        }  catch (ValidationException $e) {
            Log::error('Blad walidacji podczas aktualizacji tagu:', $e->errors());

            return response()->json([
                'message' => 'Blad walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(Tag $tag)
    {
        try {
            // Usunięcie powiązań z tagiem w tablicy pośredniej (można uprościć)
            $tag->points()->detach();

            $tag->delete();

            return response()->json([
                'message' => 'Usunięto tag'
            ], 200);

        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono tagu.',
            ], 404);
        }
    } 
}
