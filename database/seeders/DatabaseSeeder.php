<?php

namespace Database\Seeders;

use App\Models\Path;
use App\Models\Point;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $filename = './points.txt';
        $f = fopen($filename, 'r');

        if(!$f) {
            return;
        }

        while(!feof($f)) {
            $line = fgets($f);
            $line = trim($line);
            $data = preg_split('/\t+/', $line, -1, PREG_SPLIT_NO_EMPTY);

            if(count($data) < 4) continue;

            $code = $data[0];
            $easting = (float)str_replace(",", ".", $data[1]);
            $northing = (float)str_replace(",", ".", $data[2]);
            $description = $data[3];

            Point::factory()->create([
                'code' => $code,
                'description' => $description,
                'easting' => $easting,
                'northing' => $northing
            ]);
        }

        $short = [43,1,2,3,4,5,6,7,43];
        $short_path = Path::create([
            'name' => 'trasa piesza krótka'
        ]);

        foreach($short as $index=>$point){
            //error_log($index . ' / ' . $point);
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
