<?php

namespace App\Http\Controllers;

use App\Http\Resources\GeneratorPathResource;
use App\Models\Point;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use PhpParser\Node\Expr\Cast\Double;

class GeneratorServer extends Controller
{
    

    public function __invoke(Request $request)
    {
        define('MIN_POINTS', 'min_points');
        define('MAX_POINTS', 'max_points');
        define('MIN_DISTANCE', 'min_distance');
        define('MAX_DISTANCE', 'max_distance');
        define('UNLOADED_START', 'unloaded_start');
        define('PROBABILITY_FACTOR', 'probability_factor');
        define('P4-6KM2-4', [
            MIN_POINTS => 4,
            MAX_POINTS => 6,
            MIN_DISTANCE => 1.50,
            MAX_DISTANCE => 4.50,
            UNLOADED_START => false,
            PROBABILITY_FACTOR => 40
        ]);
        define('P4-6KM5-7', [
            MIN_POINTS => 4,
            MAX_POINTS => 6,
            MIN_DISTANCE => 4.50,
            MAX_DISTANCE => 7.50,
            UNLOADED_START => true,
            PROBABILITY_FACTOR => 45
        ]);
        define('P4-6KM8-10', [
            MIN_POINTS => 4,
            MAX_POINTS => 6,
            MIN_DISTANCE => 7.50,
            MAX_DISTANCE => 10.50,
            UNLOADED_START => true,
            PROBABILITY_FACTOR => 50
        ]);

        //====================//
        define('P7-9KM2-4', [
            MIN_POINTS => 7,
            MAX_POINTS => 9,
            MIN_DISTANCE => 1.50,
            MAX_DISTANCE => 4.50,
            UNLOADED_START => false,
            PROBABILITY_FACTOR => 60
        ]);
        define('P7-9KM5-7', [
            MIN_POINTS => 7,
            MAX_POINTS => 9,
            MIN_DISTANCE => 4.50,
            MAX_DISTANCE => 7.50,
            UNLOADED_START => true,
            PROBABILITY_FACTOR => 65
        ]);
        define('P7-9KM8-10', [
            MIN_POINTS => 7,
            MAX_POINTS => 9,
            MIN_DISTANCE => 7.50,
            MAX_DISTANCE => 10.50,
            UNLOADED_START => true,
            PROBABILITY_FACTOR => 70
        ]);
        
        //====================//
        define('P10-12KM2-4', [
            MIN_POINTS => 10,
            MAX_POINTS => 12,
            MIN_DISTANCE => 1.50,
            MAX_DISTANCE => 4.50,
            UNLOADED_START => false,
            PROBABILITY_FACTOR => 65
        ]);
        define('P10-12KM5-7', [
            MIN_POINTS => 10,
            MAX_POINTS => 12,
            MIN_DISTANCE => 4.50,
            MAX_DISTANCE => 7.50,
            UNLOADED_START => true,
            PROBABILITY_FACTOR => 70
        ]);
        define('P10-12KM8-10', [
            MIN_POINTS => 10,
            MAX_POINTS => 12,
            MIN_DISTANCE => 7.50,
            MAX_DISTANCE => 10.50,
            UNLOADED_START => true,
            PROBABILITY_FACTOR => 75
        ]);

        $start_point_id = $request->start_point_id;
        $end_point_id = $request->end_point_id;
        $data_set = constant($request->number_of_points_range . $request->distance_range);

        return $this->generatePaths(Point::all(), $start_point_id, $end_point_id, $data_set, 20);

    }

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

    private function calculateDistance(Point $p1, Point $p2): float 
    {
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

    private function transformPathFromIdxToPointArray(array $path_idx, Collection $points_db): array
    {
        $path_points = [];
        foreach($path_idx as $idx) {
            $path_points[] = $points_db->find($idx);
        }
        return $path_points;
    }

    private function generatePaths($db_points, $start_point_id, $end_point_id, $data_set, $number_of_paths = 1): AnonymousResourceCollection
    {
        $result = collect();
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

    function findPath($matrix, $start_point, $end_point, $min_length, $max_length, $min_points, $max_points, $probability, $unloaded_start, $current_length = 0.0, $path = [], $visited_points = [],  $tekst="/")
    {
        if(count($path) == 0) {
            $path[] = $start_point;
            $visited_points = $start_point != $end_point ? array_merge($visited_points, [$start_point]) : $visited_points;

        }

        if(
            $current_length > $max_length || 
            count($path) > $max_points
        ) {
            return null;
        }

        if(
            $start_point == $end_point &&
            ($current_length < $min_length || count($path) < $min_points) &&
            count($path) > 1
        ) {
            return null;
        }

        if(
            $start_point == $end_point && 
            $current_length >= $min_length &&
            $current_length <= $max_length &&
            count($path) >= $min_points &&
            count($path) <= $max_points
        ) {
            error_log($tekst . "; distance: " . $current_length . "; points: " . count($path));
            return $path;
        }

        if(rand(1, 100) <= $probability || $unloaded_start)
        {
            $point = array_rand($matrix);

            if(!in_array($point, $visited_points) && $point != $start_point)
            {
                $distance = $matrix[$start_point][$point];

                $result = $this->findPath($matrix, $point, $end_point, $min_length, $max_length, $min_points, $max_points, $probability, false, $current_length + $distance, array_merge($path, [$point]), array_merge($visited_points, [$point]), $tekst . $point . "/");
                
                if($result !== null) {
                    return $result;
                }
            }
        } else {
            foreach($matrix[$start_point] as $point => $distance){
                if(!in_array($point, $visited_points) && (bool)rand(0,1)){
                    $result = $this->findPath($matrix, $point, $end_point, $min_length, $max_length, $min_points, $max_points, $probability, false,  $current_length + $distance, array_merge($path, [$point]), array_merge($visited_points, [$point]), $tekst . $point*100 . "/");
                    
                    if($result !== null) {
                        return $result;
                    }
                }
            }
        }
        
        return null;
    }
}
