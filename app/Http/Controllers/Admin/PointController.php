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
    {   
        // logika endpointu STORE api/admin/points/{point}
        try {
            $validatedData = $request->validate(Point::rules());
            // dodatkowa walidacja przy dodawaniu punktów
            $request->validate([
                'code' => [
                    Rule::unique('points'), 
                ],
            ]);

            $point = Point::create($validatedData);
            return redirect()->route('admin.zpk')->with('success', 'Dodano nowy punkt');
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Blad walidacji',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function index()
    {
        $points = Point::with('pointTags')->get();
        return PointResource::collection($points)->response()->setStatusCode(200);
    }

    public function show(Point $point)
    {
        // logika endpointu GET api/admin/points/{point}
        try {
            $point->load('pointTags'); 
            return (new PointResource($point))->response()->setStatusCode(200); 
    
        } catch (\Exception $exception) {
            // Logowanie błędu - ważne dla debugowania
            Log::error($exception); 
    
            // 500 Internal Server Error: Ogólny błąd serwera
            return response()->json([
                'message' => 'Wystąpił błąd serwera.', 
            ], 500);
        }
    }
    
    public function update(Request $request, Point $point)
    {
        // logika endpointu PUT api/admin/points/{point}
        try {
            $point->findOrFail($point->id);
            // dodatkowa walidacja przy dodawaniu punktów
            $request->validate([
                'code' => [
                    Rule::unique('points')->ignore($point->id),
                ],
            ]);
            $validatedData = $request->validate(Point::rules());
            $point->update($validatedData);

            // 200 OK: Żądanie zostało obsłużone poprawnie, 
            // lub 204 No Content: Zasób został zaktualizowany, ale nie ma treści do zwrócenia
            return redirect()->route('admin.zpk')->with('success', 'Zaktualizowano punkt');
            //return response()->noContent(); 

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
    {
        // logika endpointu DELETE api/admin/points/{point}
        $point->delete();
        return response()->noContent(); 

        /* ggh TODEL:
        
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
        */
    }
}