<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\error;

class PointController extends Controller
{
    public function store(Request $request, Point $point)
    {   
        // logika endpointu STORE api/admin/points/{point}
        try {
            $validatedData = $request->validate(Point::rules());
            $point = Point::create($validatedData);
            return response()->json([
                'message' => 'Dodano nowy punkt',
                'point' => $point, 
            ], 201);
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Błąd walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function index()
    {
        $points = Point::all();
        return response()->json($points, 200);
    }

    public function destroy(Point $point)
    {
        // logika endpointu DELETE api/admin/points/{point}
        try {
            $point->findOrFail($point->id);
            $point->delete();
            // 204 No Content: Zasób został usunięty
            return response()->noContent(); 

        } catch (ModelNotFoundException $exception) {
            // 404 Not Found: Nie znaleziono zasobu
            return response()->json([
                'message' => 'Nie znaleziono punktu.',
            ], 404);
        }
    }

    public function update(Request $request, Point $point)
    {
        // logika endpointu PUT api/admin/points/{point}
        try {
            $point->findOrFail($point->id);
            $validatedData = $request->validate(Point::rules());
            $point->update($validatedData);

            // 200 OK: Żądanie zostało obsłużone poprawnie, 
            // lub 204 No Content: Zasób został zaktualizowany, ale nie ma treści do zwrócenia
            return response()->noContent(); 

        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Nie znaleziono punktu.',
            ], 404);
        } catch (ValidationException $e) {
            Log::error('Błędy walidacji podczas aktualizacji punktu:', $e->errors());

            return response()->json([
                'message' => 'Błąd walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}