<?php

namespace Database\Seeders;

use App\Models\Point;
use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // POINTS
        {
            $filename = './points.txt';
            $f = fopen($filename, 'r');

            if(!$f) {
                return;
            }

            while(!feof($f)) {
                $line = fgets($f);
                $line = trim($line);
                $data = preg_split('/\t+/', $line, -1, PREG_SPLIT_NO_EMPTY);

                if(count($data) < 5) continue;

                $code = $data[0];
                $easting = (float)str_replace(",", ".", $data[1]);
                $northing = (float)str_replace(",", ".", $data[2]);
                $pointVirtual = $data[3];
                $area_id = $data[4];
                $url = $data[5];
                $description = $data[6];

                $point = Point::factory()->create([
                    'code' => $code,
                    'description' => $description,
                    'easting' => $easting,
                    'northing' => $northing,
                    'pointVirtual' => $pointVirtual,
                    'url' => $url
                ]);
                $area = Area::find($area_id);
                $area->points()->attach($point->id); 
            }
        }
    }
}
