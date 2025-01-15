<?php

namespace App\Http\Controllers;

use App\Http\Resources\GeneratorPathResource;
use App\Models\Point;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GeneratorService extends Controller
{

    // Definicje stałych
    const MIN_POINT_COUNT = 'min_point_count';
    const MAX_POINT_COUNT = 'max_point_count';
    const MIN_DISTANCE_KM = 'min_distance_km';
    const MAX_DISTANCE_KM = 'max_distance_km';
    const NUMBER_OF_PATHS = 6;

    public function __invoke(Request $request)
    {
        $startPointId = $request->start_point_id;
        $endPointId = $request->end_point_id;
        $startPoint = Point::find($startPointId);
        $endPoint = Point::find($endPointId);

        $dataSet = $this->getDataSet($request->number_of_points_range, $request->distance_range);

        // Dodanie punktu startowego do kolekcji
        $dbPoints = (new Point())->newCollection([$startPoint]);

        // Pobieranie punktów z filtrowaniem (areas, virtualPoints: false->noVP, tags)
        $filteredPoints = Point::query();

        if (!empty($request->areas)) {
            $filteredPoints = $filteredPoints->whereHas('areas', function ($query) use ($request) {
                $query->whereIn('areas.id', $request->areas);
            });
        }

        if ($request->has('virtualpoints') && $request->virtualpoints == false) { 
            $filteredPoints = $filteredPoints->where('pointVirtual', false); 
        }

        if (!empty($request->tags)) {
            $filteredPoints = $filteredPoints->whereHas('tags', function ($query) use ($request) {
                $query->whereIn('tags.id', $request->tags);
            });
        }

        $filteredPoints = $filteredPoints->get();

        // Usunięcie punktów startowego i końcowego z filtrowanej kolekcji
        $filteredPoints = $filteredPoints->reject(function ($point) use ($startPointId, $endPointId) {
            return $point->id == $startPointId || $point->id == $endPointId;
        });

        // Dodanie przefiltrowanych punktów do kolekcji
        $dbPoints = $dbPoints->merge($filteredPoints);

        // Dodanie punktu końcowego do kolekcji
        $dbPoints->push($endPoint);

        // Sprawdzenie, czy liczba punktów jest wystarczająca
        $minPointCount = $dataSet[self::MIN_POINT_COUNT];
        $totalPoints = $dbPoints->count();

        if ($totalPoints < $minPointCount) {
            return response()->json([
                'message' => 'Niewystarczająca liczba punktów po filtrowaniu. '
                    . 'Minimalna liczba punktów: ' . $minPointCount . ', '
                    . 'dostępna liczba punktów: ' . $totalPoints
            ], 400);
        }

        return $this->generatePaths($dbPoints, $startPointId, $endPointId, $dataSet, self::NUMBER_OF_PATHS);
    }

    private function getDataSet($numberOfPointsRange, $distanceRange)
    {
        // Zakresy punktów i odległości
        $pointRanges = [
            [self::MIN_POINT_COUNT => 3, self::MAX_POINT_COUNT => 5],
            [self::MIN_POINT_COUNT => 6, self::MAX_POINT_COUNT => 8],
            [self::MIN_POINT_COUNT => 9, self::MAX_POINT_COUNT => 11],
            [self::MIN_POINT_COUNT => 12, self::MAX_POINT_COUNT => 14],
            [self::MIN_POINT_COUNT => 15, self::MAX_POINT_COUNT => 17],
            [self::MIN_POINT_COUNT => 18, self::MAX_POINT_COUNT => 21],
        ];
        $distanceRanges = [
            [self::MIN_DISTANCE_KM => 1.50, self::MAX_DISTANCE_KM => 5.50],
            [self::MIN_DISTANCE_KM => 5.50, self::MAX_DISTANCE_KM => 9.50],
            [self::MIN_DISTANCE_KM => 9.50, self::MAX_DISTANCE_KM => 12.50],
            [self::MIN_DISTANCE_KM => 12.50, self::MAX_DISTANCE_KM => 15.50],
            [self::MIN_DISTANCE_KM => 15.50, self::MAX_DISTANCE_KM => 18.50],
            [self::MIN_DISTANCE_KM => 18.50, self::MAX_DISTANCE_KM => 24.50],
            // [self::MIN_DISTANCE_KM => 19.50, self::MAX_DISTANCE_KM => 21.50],
        ];

        // Indeksy zakresów
        $pointRangeIndex = array_search($numberOfPointsRange, [
            'P3-5', 
            'P6-8', 
            'P9-11', 
            'P12-14', 
            'P15-17', 
            'P18-21', 
        ]);
        $distanceRangeIndex = array_search($distanceRange, [
            'KM2-5', 
            'KM6-9', 
            'KM10-12', 
            'KM13-15', 
            'KM16-18', 
            'KM19-24', 
        ]);

        // Zakresy punktów i odległości na podstawie indeksów
        $pointRange = $pointRanges[$pointRangeIndex];
        $distanceRange = $distanceRanges[$distanceRangeIndex];

        // Zestaw danych
        return array_merge($pointRange, $distanceRange);
    }

    private function calculateDistance(Point $p1, Point $p2): float
    {
        // Konwersja jednostek z metrów na kilometry
        $x1 = $p1->easting / 1000;
        $y1 = $p1->northing / 1000;
        $x2 = $p2->easting / 1000;
        $y2 = $p2->northing / 1000;

        $deltaX = $x1 - $x2;
        $deltaXSq = pow($deltaX, 2);
        $deltaY = $y1 - $y2;
        $deltaYSq = pow($deltaY, 2);

        return sqrt($deltaXSq + $deltaYSq);
    }

    private function transformPathFromIdxToPointArray(array $pathIdx, Collection $pointsDb): array
    {
        $pathPoints = [];
        foreach ($pathIdx as $idx) {
            $pathPoints[] = $pointsDb->find($idx);
        }
        return $pathPoints;
    }

    private function generatePaths(Collection $dbPoints, $startPointId, $endPointId, $dataSet, $numberOfPaths = 1): AnonymousResourceCollection
    {
        $result = new Collection();
        $maxAttemps = 400;

        for ($i = 0; $i < $numberOfPaths; $i++) {
            $pathIdx = null;
            $attemps = 0;
            
            $avaliablePoints = $dbPoints->map(function ($point) {
                return $point;
            });

            while ($pathIdx == null && $attemps < $maxAttemps) {
                $pathIdx = $this->findPath(
                    $avaliablePoints,
                    $startPointId,
                    $endPointId,
                    $dataSet[self::MIN_DISTANCE_KM],
                    $dataSet[self::MAX_DISTANCE_KM],
                    $dataSet[self::MIN_POINT_COUNT],
                    $dataSet[self::MAX_POINT_COUNT],
                );
                $attemps++;
            }

            // Dodanie ścieżki do wyniku, jeśli została znaleziona
            if ($pathIdx) {
                $pathPoints = $this->transformPathFromIdxToPointArray($pathIdx, $dbPoints);
                $route = ['id' => $i, 'points' => $pathPoints];
                $result->push((object)$route);
            } 
        }

        return GeneratorPathResource::collection($result);
    }

    function findPath(
        $avaliablePoints, 
        $startPoint, $endPoint,
        $minLength, $maxLength,
        $minPoints, $maxPoints,
    ) 
    {

        // Inicjalizacja ścieżki
        $path[] = $startPoint;
        $currentPoint = $startPoint;
        $currentLength = 0.0;

        $avaliablePoints = $avaliablePoints->filter(function ($point) use ($startPoint) {
            return $point->id != $startPoint; 
        });

        while (
            $currentPoint != $endPoint && 
            count($path) < $maxPoints && 
            $currentLength < $maxLength &&
            $avaliablePoints->isNotEmpty()   
        ) {
            // Losujemy punkt z dostępnych
            $randomPoint = $avaliablePoints->random();

            // Dodajemy punkt do ścieżki
            $path[] = $randomPoint->id;

            // Aktualizujemy parametry ścieżki
            $currentPointObject = Point::find($currentPoint);
            $currentLength += $this->calculateDistance($currentPointObject, $randomPoint);
            // dd([
            //         'zmienna' => $currentLength,
            // ]);
            $currentPoint = $randomPoint->id;

            // Usuwamy wylosowany punkt z $dbPoints (i punkt startowy w pierwszej iteracji)
            $avaliablePoints = $avaliablePoints->filter(function ($point) use ($randomPoint, $startPoint) {
                return $point->id != $randomPoint->id; 
            });
        }
        // Sprawdzenie, czy ścieżka spełnia założenia
        if (
            $currentLength >= $minLength && 
            count($path) >= $minPoints &&
            end($path) == $endPoint) {
            return $path; // Zwracamy ścieżkę, jeśli spełnia kryteria
        } else {
            return null; // Zwracamy null, jeśli ścieżka nie spełnia kryteriów
        }
    }
}

