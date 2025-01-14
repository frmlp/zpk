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
    const IS_UNLOADED_START = 'is_unloaded_start';
    const PROBABILITY_FACTOR_PERCENT = 'probability_factor_percent';    

    public function __invoke(Request $request)
    {
        
        $startPointId = $request->start_point_id;
        $endPointId = $request->end_point_id;
        $startPoint = Point::find($startPointId);
        $endPoint = Point::find($endPointId);

        $dataSet = $this->getDataSet($request->number_of_points_range, $request->distance_range);

        // Dodanie punktu startowego do kolekcji
        $dbPoints = (new Point())->newCollection([$startPoint]);

        // Pobieranie punktów z filtrowaniem
        $filteredPoints = Point::query();
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

        return $this->generatePaths($dbPoints, $startPointId, $endPointId, $dataSet, 4);
    }

    private function getDataSet($numberOfPointsRange, $distanceRange)
    {
        // Zakresy punktów i odległości
        $pointRanges = [
            [self::MIN_POINT_COUNT => 4, self::MAX_POINT_COUNT => 6],
            [self::MIN_POINT_COUNT => 7, self::MAX_POINT_COUNT => 9],
            [self::MIN_POINT_COUNT => 10, self::MAX_POINT_COUNT => 12],
            [self::MIN_POINT_COUNT => 13, self::MAX_POINT_COUNT => 15],
            [self::MIN_POINT_COUNT => 16, self::MAX_POINT_COUNT => 18],
            [self::MIN_POINT_COUNT => 19, self::MAX_POINT_COUNT => 21],
            [self::MIN_POINT_COUNT => 22, self::MAX_POINT_COUNT => 24],
        ];
        $distanceRanges = [
            [self::MIN_DISTANCE_KM => 1.50, self::MAX_DISTANCE_KM => 4.50],
            [self::MIN_DISTANCE_KM => 4.50, self::MAX_DISTANCE_KM => 7.50],
            [self::MIN_DISTANCE_KM => 7.50, self::MAX_DISTANCE_KM => 10.50],
            [self::MIN_DISTANCE_KM => 10.50, self::MAX_DISTANCE_KM => 13.50],
            [self::MIN_DISTANCE_KM => 13.50, self::MAX_DISTANCE_KM => 16.50],
            [self::MIN_DISTANCE_KM => 16.50, self::MAX_DISTANCE_KM => 19.50],
            [self::MIN_DISTANCE_KM => 19.50, self::MAX_DISTANCE_KM => 21.50],
            [self::MIN_DISTANCE_KM => 22.50, self::MAX_DISTANCE_KM => 24.50],
        ];

        // Indeksy zakresów
        $pointRangeIndex = array_search($numberOfPointsRange, ['P4-6', 'P7-9', 'P10-12', 'P13-15', 'P16-18', 'P19-21', 'P22-24']);
        $distanceRangeIndex = array_search($distanceRange, ['KM2-4', 'KM5-7', 'KM8-10', 'KM11-13', 'KM14-16', 'KM17-19', 'KM20-22']);

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

    // Przekształcenie tablicy indeksów na tablicę obiektów Point
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
        for ($i = 0; $i < $numberOfPaths; $i++) {
            $pathIdx = null;
            while ($pathIdx == null) {
                $pathIdx = $this->findPath(
                    $dbPoints,
                    $startPointId,
                    $endPointId,
                    $dataSet[self::MIN_DISTANCE_KM],
                    $dataSet[self::MAX_DISTANCE_KM],
                    $dataSet[self::MIN_POINT_COUNT],
                    $dataSet[self::MAX_POINT_COUNT],
                );
            }

            $pathPoints = $this->transformPathFromIdxToPointArray($pathIdx, $dbPoints);
            $route = ['id' => $i, 'points' => $pathPoints];

            $result->push((object)$route);
        }

        return GeneratorPathResource::collection($result);
    }


    // główna funkcja tworząca ścieżki
    /*  OPIS ALGORYTMU:
        todo        
    */
    function findPath(
        $dbPoints, $startPoint, $endPoint,
        $minLength, $maxLength,
        $minPoints, $maxPoints,
        $currentLength = 0.0,
        $path = [],
        $visitedPoints = []
    ) 
    {
        // Inicjalizacja ścieżki
        $path[] = $startPoint;
        $visitedPoints[$startPoint] = true;
        $currentPoint = $startPoint;
        $currentLength = 0.0;

        while ($currentPoint != $endPoint) {
            // Wybór losowego punktu z puli
            $availablePoints = $dbPoints->whereNotIn('id', array_keys($visitedPoints));
            $randomPoint = $availablePoints->random();
            $randomPoint = $dbPoints->firstWhere('id', $randomPoint->id);

            // Dodanie punktu do ścieżki
            $path[] = $randomPoint->id;
            $visitedPoints[$randomPoint->id] = true;

            // Aktualizacja parametrów ścieżki
            $currentPointObject = $dbPoints->firstWhere('id', $currentPoint);
            $currentLength += $this->calculateDistance($currentPointObject, $randomPoint);
            // $currentLength += $this->calculateDistance($dbPoints[$currentPoint], $randomPoint);
            $currentPoint = $randomPoint->id;
            $startX = $randomPoint->easting;
            $startY = $randomPoint->northing;

            // Sprawdzenie parametrów ścieżki
            if ($currentLength > $maxLength || count($path) > $maxPoints) {
                // Parametry przekroczone - restart
                return $this->findPath(
                    $dbPoints,
                    $startPoint,
                    $endPoint,
                    $minLength,
                    $maxLength,
                    $minPoints,
                    $maxPoints,
                );
            }
        }

        // Sprawdzenie, czy ścieżka spełnia założenia
        if ($currentLength >= $minLength && count($path) >= $minPoints) {
            return $path;
        } else {
            // Ścieżka nie spełnia założeń - restart
            return $this->findPath(
                $dbPoints,
                $startPoint,
                $endPoint,
                $minLength,
                $maxLength,
                $minPoints,
                $maxPoints,
            );
        }
    }
}

