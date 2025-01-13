<?php

namespace App\Http\Controllers;

use App\Http\Resources\GeneratorPathResource;
use App\Models\Point;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use PhpParser\Node\Expr\Cast\Double;

class GeneratorService extends Controller
{

    public function __invoke(Request $request)
    {
        // definicja stałych, nie wiem czy to tutaj powinno być, nie wiem czy w takiej formie
        define('MIN_POINTS', 'min_points');
        define('MAX_POINTS', 'max_points');
        define('MIN_DISTANCE', 'min_distance');
        define('MAX_DISTANCE', 'max_distance');
        define('UNLOADED_START', 'unloaded_start');
        define('PROBABILITY_FACTOR', 'probability_factor');
        
        { // poprzednia definicja setów
            // define('P4-6KM2-4', [
            //     MIN_POINTS => 4,
            //     MAX_POINTS => 6,
            //     MIN_DISTANCE => 1.50,
            //     MAX_DISTANCE => 4.50,
            //     UNLOADED_START => false,
            //     PROBABILITY_FACTOR => 40
            // ]);
            // define('P4-6KM5-7', [
            //     MIN_POINTS => 4,
            //     MAX_POINTS => 6,
            //     MIN_DISTANCE => 4.50,
            //     MAX_DISTANCE => 7.50,
            //     UNLOADED_START => true,
            //     PROBABILITY_FACTOR => 45
            // ]);
            // define('P4-6KM8-10', [
            //     MIN_POINTS => 4,
            //     MAX_POINTS => 6,
            //     MIN_DISTANCE => 7.50,
            //     MAX_DISTANCE => 10.50,
            //     UNLOADED_START => true,
            //     PROBABILITY_FACTOR => 50
            // ]);

            // //====================//
            // define('P7-9KM2-4', [
            //     MIN_POINTS => 7,
            //     MAX_POINTS => 9,
            //     MIN_DISTANCE => 1.50,
            //     MAX_DISTANCE => 4.50,
            //     UNLOADED_START => false,
            //     PROBABILITY_FACTOR => 60
            // ]);
            // define('P7-9KM5-7', [
            //     MIN_POINTS => 7,
            //     MAX_POINTS => 9,
            //     MIN_DISTANCE => 4.50,
            //     MAX_DISTANCE => 7.50,
            //     UNLOADED_START => true,
            //     PROBABILITY_FACTOR => 65
            // ]);
            // define('P7-9KM8-10', [
            //     MIN_POINTS => 7,
            //     MAX_POINTS => 9,
            //     MIN_DISTANCE => 7.50,
            //     MAX_DISTANCE => 10.50,
            //     UNLOADED_START => true,
            //     PROBABILITY_FACTOR => 70
            // ]);
            
            // //====================//
            // define('P10-12KM2-4', [
            //     MIN_POINTS => 10,
            //     MAX_POINTS => 12,
            //     MIN_DISTANCE => 1.50,
            //     MAX_DISTANCE => 4.50,
            //     UNLOADED_START => false,
            //     PROBABILITY_FACTOR => 65
            // ]);
            // define('P10-12KM5-7', [
            //     MIN_POINTS => 10,
            //     MAX_POINTS => 12,
            //     MIN_DISTANCE => 4.50,
            //     MAX_DISTANCE => 7.50,
            //     UNLOADED_START => true,
            //     PROBABILITY_FACTOR => 70
            // ]);
            // define('P10-12KM8-10', [
            //     MIN_POINTS => 10,
            //     MAX_POINTS => 12,
            //     MIN_DISTANCE => 7.50,
            //     MAX_DISTANCE => 10.50,
            //     UNLOADED_START => true,
            //     PROBABILITY_FACTOR => 75
            // ]);
        }

        $start_point_id = $request->start_point_id;
        $end_point_id = $request->end_point_id;
        $start_point = Point::find($start_point_id);
        $end_point = Point::find($end_point_id);
        $data_set = $this->getDataSet($request->number_of_points_range, $request->distance_range);
        // $data_set = constant($request->number_of_points_range . $request->distance_range);
        
        // Pobieranie punktów z filtrowaniem
        $db_points = Point::query();
        if (!empty($request->tags)) {
            $db_points = $db_points->whereHas('tags', function ($query) use ($request) {
                $query->whereIn('tags.id', $request->tags);
            });
        }
        $db_points = $db_points->get();
        $db_points = $db_points->push($start_point, $end_point);
        
        return $this->generatePaths($db_points, $start_point_id, $end_point_id, $data_set, 8);
        //return $this->generatePaths(Point::all(), $start_point_id, $end_point_id, $data_set, 8);

    }

    private function getDataSet($numberOfPointsRange, $distanceRange)
    {
        // Definiujemy zakresy punktów i odległości
        $pointRanges = [
            [MIN_POINTS => 4, MAX_POINTS => 6],
            [MIN_POINTS => 7, MAX_POINTS => 9],
            [MIN_POINTS => 10, MAX_POINTS => 12],
            [MIN_POINTS => 13, MAX_POINTS => 15],
            [MIN_POINTS => 16, MAX_POINTS => 18],
            [MIN_POINTS => 19, MAX_POINTS => 21],
            [MIN_POINTS => 22, MAX_POINTS => 24],
        ];
        $distanceRanges = [
            [MIN_DISTANCE => 1.50, MAX_DISTANCE => 4.50],
            [MIN_DISTANCE => 4.50, MAX_DISTANCE => 7.50],
            [MIN_DISTANCE => 7.50, MAX_DISTANCE => 10.50],
            [MIN_DISTANCE => 10.50, MAX_DISTANCE => 13.50],
            [MIN_DISTANCE => 13.50, MAX_DISTANCE => 16.50],
            [MIN_DISTANCE => 16.50, MAX_DISTANCE => 19.50],
            [MIN_DISTANCE => 19.50, MAX_DISTANCE => 21.50],
            [MIN_DISTANCE => 22.50, MAX_DISTANCE => 24.50],
        ];

        // Indeksy zakresów z argumentów funkcji
        $pointRangeIndex = array_search($numberOfPointsRange, ['P4-6', 'P7-9', 'P10-12', 'P13-15', 'P16-18', 'P19-21', 'P22-24']);
        $distanceRangeIndex = array_search($distanceRange, ['KM2-4', 'KM5-7', 'KM8-10', 'KM11-13', 'KM14-16', 'KM17-19', 'KM20-22']);

        // Zakresy punktów i odległości na podstawie indeksów
        $pointRange = $pointRanges[$pointRangeIndex];
        $distanceRange = $distanceRanges[$distanceRangeIndex];

        // PROBABILITY_FACTOR
        $probabilityFactor = 40 + ($pointRangeIndex * 15) + ($distanceRangeIndex * 5);
        $probabilityFactor = min($probabilityFactor, 100); 

        // Zestaw danych
        $dataSet = array_merge($pointRange, $distanceRange, [
            UNLOADED_START => $distanceRangeIndex > 0, // UNLOADED_START jest true dla wszystkich zakresów odległości oprócz pierwszego
            PROBABILITY_FACTOR => $probabilityFactor
        ]);

        return $dataSet;
    }

    // funkcja tworząca macierz grafu - klika o ważonych krawędziach
    // waga krawędzi jest odległością w kilometrach między punktami
    private function buildMatrix(Collection $points): array
    {
        $matrix = [];

        for($i = 0; $i < count($points); $i++)
        {
            $p1 = $points[$i];
            for($j = $i + 1 ; $j < count($points); $j++)
            {
                $p2 = $points[$j];
                $distance = $this->calculateDistance($p1, $p2);
                $matrix[$p1->id][$p2->id] = $distance;
                $matrix[$p2->id][$p1->id] = $distance;  
            }
        }

        return $matrix;
    }

    //funkcja, która liczy odległość między punktami z tw. Pitagorasa
    private function calculateDistance(Point $p1, Point $p2): float 
    {
        // konwersja jednostki z metrów na kilometry
        $x1 = $p1->easting/1000;
        $y1 = $p1->northing/1000;
        $x2 = $p2->easting/1000;
        $y2 = $p2->northing/1000;

        $delta_x = $x1 - $x2;
        $delta_x_sq = pow($delta_x, 2);
        $delta_y = $y1 - $y2;
        $delta_y_sq = pow($delta_y, 2);

        $distance = sqrt($delta_x_sq + $delta_y_sq);

        return $distance;
    }

    // funkcja, która z tablicy indeksów, tworzy tablicę obiektów Punktów;
    // generator tworzy tablicę indeksów, bo indeksy ułatwiają pracę z macierzą
    // na koniec na frontend musimy wysłać obiekty Punktów z wszystkimi informacjami
    


    // poprzednia wersja
    private function transformPathFromIdxToPointArray(array $path_idx, Collection $points_db): array
    {
        $path_points = [];
        foreach($path_idx as $idx) {
            $path_points[] = $points_db->find($idx);
        }
        return $path_points;
    }

    private function generatePaths($db_points, $start_point_id, $end_point_id, $data_set, $number_of_paths = 1, $tags = []): AnonymousResourceCollection
    {
        $result = collect();

        // filtrowanie po tagach
        if (!empty($tags)) {
            $db_points = $db_points->whereHas('tags', function ($query) use ($tags) {
                $query->whereIn('tags.id', $tags);
            });
        }

        $matrix = $this->buildMatrix($db_points);

        for($i = 0; $i < $number_of_paths; $i++) {
            $path_idx = null;
        
            while($path_idx == null){
                $path_idx = $this->findPath(
                    $matrix, 
                    $start_point_id, 
                    $end_point_id, 
                    $data_set[MIN_DISTANCE], 
                    $data_set[MAX_DISTANCE], 
                    $data_set[MIN_POINTS], 
                    $data_set[MAX_POINTS],
                    $data_set[PROBABILITY_FACTOR],
                    $data_set[UNLOADED_START],
                );
            }

            $path_points = $this->transformPathFromIdxToPointArray($path_idx, $db_points);

            $route = ['id' => $i, 'points' => $path_points];

            $result->push((object)$route);
        }

        return GeneratorPathResource::collection($result);
    }

    // główna funkcja tworząca ścieżki
    // ogólnie to jest algorytm DFS 
    // algorytm DFS dla naszej liczby punktów działa za długo, więc część punktów jest wybierana losowo
    // im więcej punktów kontrolnych z których ma się składać trasa, lub im dłuższa trasa, tym więcej punktów jest wybieranych losowo (40-75%)
    // do tego losowo wybierane punkty urozmaicają tworzone trasy, inaczej były drobne różnice między generowanymi ścieżkami
    // $unloaded_start - boolean - czy pierwszy punkt ma być wybrany losowo, czy też nie
    // poprawia różnorodność krótkich tras
    function findPath($matrix, $start_point, $end_point, $min_length, $max_length, $min_points, $max_points, $probability, $unloaded_start, $current_length = 0.0, $path = [], $visited_points = [],  $tekst="/")
    {
        if(count($path) == 0) {
            $path[] = $start_point;

            // tablica odwiedzonych punktów
            // dla trasy otwartej musimy dodać punkt startowy, żeby nie był brany pod uwagę
            // dla pętli nie ma takiej potrzeby bo jest to równoznaczne z zakończeniem trasy
            $visited_points = $start_point != $end_point ? array_merge($visited_points, [$start_point]) : $visited_points;

        }

        // wyjście z funkcji (null), jeśli wygenerowana trasa jest za długa lub ma za dużo punktów
        if(
            $current_length > $max_length || 
            count($path) > $max_points
        ) {
            return null;
        }

        // wyjście z funkcji (null), jeśli doszliśmy do punktu końcowego, ale nasza trasa jest za krótka lub ma za mało punktów kontrolnych
        if(
            $start_point == $end_point &&
            ($current_length < $min_length || count($path) < $min_points) &&
            count($path) > 1
        ) {
            return null;
        }

        // wyjście z funkcji ($path - tablica indeksów składających się na trasę) jeśli wygenerowana trasa spełnia warunki
        if(
            $start_point == $end_point && 
            $current_length >= $min_length &&
            $current_length <= $max_length &&
            count($path) >= $min_points &&
            count($path) <= $max_points
        ) {
            
            return $path;
        }

        
        if(rand(1, 100) <= $probability || $unloaded_start) // warunek wybrania losowego punktu, lub pierwszy p[unkt wybierany losowo
        {
            //wylosuj punkt z macierzy
            $point = array_rand($matrix);

            // sprawdź czy punkt nie został już odwiedzony, lub czy nie jest punktem startowym
            if(!in_array($point, $visited_points) && $point != $start_point)
            {
                //sprawdź odległość między punktami w macierzy
                $distance = $matrix[$start_point][$point];

                // rekurencyjne wywołanie funkcji z zaktualizowanymi argumentami
                $result = $this->findPath($matrix, $point, $end_point, $min_length, $max_length, $min_points, $max_points, $probability, false, $current_length + $distance, array_merge($path, [$point]), array_merge($visited_points, [$point]), $tekst . $point . "/");
                
                // jeśli udało się stworzyć trasę, zwróć ją
                if($result !== null) {
                    return $result;
                }
            }
        } else { // punkt wybrany z algorytmu DFS
            foreach($matrix[$start_point] as $point => $distance){
                if(!in_array($point, $visited_points) && (bool)rand(0,1)){ // sprawdź czy punkt nie został już odwiedzony i odrzuć co drugi punkt???? nie pamiętam po co to zrobiłem. Pewnie algorytm był za wolny.
                    
                    // rekurencyjne wywołanie funkcji z zaktualizowanymi argumentami
                    $result = $this->findPath($matrix, $point, $end_point, $min_length, $max_length, $min_points, $max_points, $probability, false,  $current_length + $distance, array_merge($path, [$point]), array_merge($visited_points, [$point]), $tekst . $point*100 . "/");
                    
                    //jeśli udało się stworzyć trasę, zwróć ją
                    if($result !== null) {
                        return $result;
                    }
                }
            }
        }
        
        // nic się nie udało xD
        return null;
    }


}
