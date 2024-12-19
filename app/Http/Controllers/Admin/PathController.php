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
    public function store(Request $request, Path $path)
    {   // logika endpointu POST api/admin/points/
        try {
            $validatedData = $request->validate(Path::rules());
            $path = Path::create($validatedData);

            $points = $request->input('points', []);
            foreach ($points as $index => $pointId) {
                $path->points()->attach($pointId, ['position' => $index + 1]);
            }

            return redirect()->route('admin.zpk')->with('success', 'Dodano nową ścieżkę');

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
    {   // logika endpointu PUT api/admin/paths/{path}
        try {
            $path->findOrFail($path->id);
            $validatedData = $request->validate(Path::rules());
            $path->update($validatedData);
    
            $existingPoints = $path->points()->get();
    
            foreach ($request->input('points', []) as $index => $pointId) {
                $point = $existingPoints->firstWhere('id', $pointId);
    
                if ($point) {
                    $path->points()->updateExistingPivot($pointId, ['position' => $index + 1]);
                } else {
                    $path->points()->attach(Point::find($pointId), ['position' => $index + 1]);
                }
            }
    
            return redirect()->route('admin.zpk')->with('success', 'Zaktualizowano ścieżkę');
    
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono ścieżki.',
            ], 404);
        } catch (ValidationException $e) {
            Log::error('Błędy walidacji podczas aktualizacji ścieżki:', $e->errors());
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