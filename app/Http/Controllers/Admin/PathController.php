<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PathResource;
use App\Models\Path;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
// use Illuminate\Validation\Rule;

class PathController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate(Path::rules());
            $path = Path::create($validatedData);

            $points = $request->input('points', []);

            // Walidacja
            for ($i = 0; $i < count($points) - 1; $i++) {
                if ($points[$i] === $points[$i + 1]) {
                    return response()->json([
                        'message' => 'Błąd walidacji: Ten sam punkt nie może występować na dwóch kolejnych pozycjach.',
                    ], 422);
                }
            }

            foreach ($points as $index => $pointId) {
                $path->points()->attach($pointId, ['position' => $index]);
            }

            return response()->json([
                'message' => 'Dodano nową ścieżkę',
                'path' => new PathResource($path) // Możesz dodać dane ścieżki do odpowiedzi
            ], 201); 

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Błąd walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }


    public function index()
    {   // logika endpointu GET api/admin/paths/
        $paths = Path::with('points')->get();
        return PathResource::collection($paths)->response()->setStatusCode(200);
    }

    public function show(Path $path)
    {   // logika endpointu GET api/admin/paths/{path}
        try {
            return new PathResource($path->load('points'));

        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json([
                'message' => 'Wystąpił błąd serwera.',
            ], 500);
        }
    }

    public function update(Request $request, Path $path)
    {
        try {
            $validatedData = $request->validate(Path::rules());
            $path->update($validatedData);
    
            $points = $request->input('points', []);
    
            // Walidacja
            for ($i = 0; $i < count($points) - 1; $i++) {
                if ($points[$i] === $points[$i + 1]) {
                    return response()->json([
                        'message' => 'Błąd walidacji: Ten sam punkt nie może występować na dwóch kolejnych pozycjach.',
                    ], 422);
                }
            }
    
            // Usunięcie istniejących powiązań
            $path->points()->detach();
    
            // Dodanie nowych powiązań
            foreach ($points as $index => $pointId) {
                $path->points()->attach($pointId, ['position' => $index]);
            }
    
            return response()->json([
                'message' => 'Zaktualizowano ścieżkę',
                'path' => new PathResource($path) 
            ], 200); 
    
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono ścieżki.',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Błąd walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }
    
    public function destroy(Path $path)
    {   // logika endpointu DELETE api/admin/paths/{path}
        try {
            $path->findOrFail($path->id);
            $path->points()->detach();
            $path->delete();

            return response()->noContent();

        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono ścieżki.',
            ], 404);
        }
    }
}