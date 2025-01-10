<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Path;
use App\Models\Point;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    
        // USERS
        {
            User::factory()->create([
                'name' => 'admin',
            ]);
        }

        // AREAS
        {
            Area::create(['id' => 1, 'name' => 'Grabówek']);
            Area::create(['id' => 2, 'name' => 'Chylonia']);
            Area::create(['id' => 3, 'name' => 'Grabówek i Chylonia']);
        }
            
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

        // POINT_TAGS
        {    
            $filename = './tags.txt';
            $f = fopen($filename, 'r');
            if(!$f) {
                return;
            }
            while(!feof($f)) {
                $line = fgets($f);
                $line = trim($line);
                $pattern = "/^##?\s.*/m";
                $data = preg_split($pattern, $line, -1, PREG_SPLIT_NO_EMPTY);

                if(count($data) < 1) continue;

                $tag = $data[0];

                Tag::factory()->create([
                    'name' => $tag
                ]);
            }

            // attach random tags to points
            $tags = Tag::all();
            Point::all()->each(function ($point) use ($tags) {
                $randomTags = $tags->random(2); 
                $point->tags()->attach($randomTags); 
            });
        }

        // PATHS
        {
            $short = [43,1,2,3,4,5,6,7,43];
            $short_path = Path::create([
                'name' => 'trasa piesza krótka'
            ]);
            foreach($short as $index=>$point){
                $short_path->points()->attach($point, ['position' => $index]);
            }

            $medium = [43,8,3,9,4,42,10,11,12,5,2,43];
            $medium_path = Path::create([
                'name' => 'trasa piesza średnia'
            ]);
            foreach($medium as $index => $point){
                $medium_path->points()->attach($point, ['position' => $index]);
            }

            $long = [43,8,13,40,39,42,10,11,14,15,16,17,12,5,9,18,43];
            $long_path = Path::create([
                'name' => 'trasa piesza długa'
            ]);
            foreach($long as $index => $point){
                $long_path->points()->attach($point, ['position' => $index]);
            }
        }
        
    }
}
