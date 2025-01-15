<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // AREAS
        {
            $filename = './areas.txt';
            $f = fopen($filename, 'r');
            $line = fgets($f);  // nagłówki

            while (!feof($f)) {
                $line = fgets($f);
                $line = trim($line);
                $data = preg_split('/\t+/', $line, -1, PREG_SPLIT_NO_EMPTY);

                if (count($data) < 6) continue; // Pomijamy linie z niepełnymi danymi

                $id = (int)$data[0];
                $name = $data[1];
                $min_easting = (float)str_replace(",", ".", $data[2]);
                $min_northing = (float)str_replace(",", ".", $data[3]);
                $max_easting = (float)str_replace(",", ".", $data[4]);
                $max_northing = (float)str_replace(",", ".", $data[5]);

                Area::create([
                    'name' => $name,
                    'min_easting' => $min_easting,
                    'min_northing' => $min_northing,
                    'max_easting' => $max_easting,
                    'max_northing' => $max_northing,
                ]);
            }
            fclose($f);
        }
    }
}
