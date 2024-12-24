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
                'message' => 'Blad walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }
    
    public function index()
    {
        $tags = Tag::all();
        return TagResource::collection($tags);
    }

    public function show(Tag $tag)
    {
        return new TagResource($tag); 
    }

    public function update(Request $request, Tag $tag)
    {
        // logika endpointu PUT api/admin/tags/{point_tag}
        try {
            $tag->findOrFail($tag->id);
            $request->validate([
                'name' => [
                    Rule::unique('tags')->ignore($tag->id),
                ],
            ]);
            $validatedData = $request->validate(Tag::rules());
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
