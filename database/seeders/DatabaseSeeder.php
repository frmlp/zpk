<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\MapFile;
use App\Models\MapPage;
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

    

        // MAP_FILES
        {
            MapFile::create(['name' => 'Mapa BnO (Biegu na orientację)', 'path' => 'app/public/maps/orienteering_full.pdf', 'icon_path' => 'icons/maps/orienteering_full.jpg']);

            MapFile::create(['name' => 'Mapa BnO (Biegu na orientację) bez dróg', 'path' => 'app/public/maps/orienteering_no_roads.pdf', 'icon_path' => 'icons/maps/orienteering_no_roads.jpg']);

            MapFile::create(['name' => 'Mapa BnO (Biegu na orientację) z samymi drogami', 'path' => 'app/public/maps/orienteering_roads_only.pdf', 'icon_path' => 'icons/maps/orienteering_roads_only.jpg']);
            
            MapFile::create(['name' => 'Mapa topograficzna', 'path' => 'app/public/maps/topografic.pdf', 'icon_path' => 'icons/maps/topografic.jpg']);

            MapFile::create(['name' => 'Mapa warstwicowa', 'path' => 'app/public/maps/contour.pdf', 'icon_path' => 'icons/maps/contour.jpg']);

            MapFile::create(['name' => 'Mapa warstwicowa z infrastrukturą', 'path' => 'app/public/maps/contour_infra.pdf', 'icon_path' => 'icons/maps/contour_infra.jpg']);

            MapFile::create(['name' => 'Mapa hipsometryczna (NMT)', 'path' => 'app/public/maps/hipsometric.pdf', 'icon_path' => 'icons/maps/hipsometric.jpg']);

            MapFile::create(['name' => 'Mapa historyczna', 'path' => 'app/public/maps/historic.pdf', 'icon_path' => 'icons/maps/historic.jpg']);

        }

        // Map_PAGES
        {
            MapPage::create([
                'map_file_id' => MapFile::where('path', 'app/public/maps/orienteering_full.pdf')->first()->id, 
                'page' => 1, 
                'coeff_a' => 0.282746, 
                'coeff_b' => -0.021378, 
                'coeff_c' => -115546.575, 
                'coeff_d' => 0.021679, 
                'coeff_e' => 0.283039, 
                'coeff_f' => -218790.281,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('path', 'app/public/maps/orienteering_full.pdf')->first()->id, 
                'page' => 2, 
                'coeff_a' => 0.282330, 
                'coeff_b' => -0.021516, 
                'coeff_c' => -115252.324, 
                'coeff_d' => 0.021724, 
                'coeff_e' => 0.283076, 
                'coeff_f' => -219265.328,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('path', 'app/public/maps/orienteering_full.pdf')->first()->id, 
                'page' => 3, 
                'coeff_a' => 0.283608, 
                'coeff_b' => -0.022023, 
                'coeff_c' => -115945.111, 
                'coeff_d' => 0.020491, 
                'coeff_e' => 0.283159, 
                'coeff_f' => -218323.798,
            ]);

            // ==========================================================================
            MapPage::create([
                'map_file_id' => MapFile::where('path', 'app/public/maps/orienteering_no_roads.pdf')->first()->id, 
                'page' => 1, 
                'coeff_a' => 0.282746, 
                'coeff_b' => -0.021378, 
                'coeff_c' => -115546.575, 
                'coeff_d' => 0.021679, 
                'coeff_e' => 0.283039, 
                'coeff_f' => -218790.281,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('path', 'app/public/maps/orienteering_no_roads.pdf')->first()->id, 
                'page' => 2, 
                'coeff_a' => 0.282330, 
                'coeff_b' => -0.021516, 
                'coeff_c' => -115250.324, 
                'coeff_d' => 0.021724, 
                'coeff_e' => 0.283076, 
                'coeff_f' => -219264.328,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('path', 'app/public/maps/orienteering_no_roads.pdf')->first()->id, 
                'page' => 3, 
                'coeff_a' => 0.283608, 
                'coeff_b' => -0.022023, 
                'coeff_c' => -115944.611, 
                'coeff_d' => 0.020491, 
                'coeff_e' => 0.283159, 
                'coeff_f' => -218322.298,
            ]);
            //==========================================================================
            MapPage::create([
                'map_file_id' => MapFile::where('path', 'app/public/maps/orienteering_roads_only.pdf')->first()->id, 
                'page' => 1, 
                'coeff_a' => 0.282746, 
                'coeff_b' => -0.021378, 
                'coeff_c' => -115546.575, 
                'coeff_d' => 0.021679, 
                'coeff_e' => 0.283039, 
                'coeff_f' => -218795.281,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('path', 'app/public/maps/orienteering_roads_only.pdf')->first()->id, 
                'page' => 2, 
                'coeff_a' => 0.282330, 
                'coeff_b' => -0.021516, 
                'coeff_c' => -115251.324, 
                'coeff_d' => 0.021724, 
                'coeff_e' => 0.283076, 
                'coeff_f' => -219265.828,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('path', 'app/public/maps/orienteering_roads_only.pdf')->first()->id, 
                'page' => 3, 
                'coeff_a' => 0.283608, 
                'coeff_b' => -0.022023, 
                'coeff_c' => -115945.611, 
                'coeff_d' => 0.020491, 
                'coeff_e' => 0.283159, 
                'coeff_f' => -218322.298,
            ]);
            //==========================================================================


            MapPage::create([
                'map_file_id' => MapFile::where('name', 'Mapa topograficzna')->first()->id, 
                'page' => 1, 
                'coeff_a' => 0.2838567, 
                'coeff_b' => 0.0, 
                'coeff_c' => -131909.927, 
                'coeff_d' => 0.0, 
                'coeff_e' => 0.2837924, 
                'coeff_f' => -209670.087,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('name', 'Mapa topograficzna')->first()->id, 
                'page' => 2, 
                'coeff_a' => 0.2670136, 
                'coeff_b' => 0.0, 
                'coeff_c' => -124458.643, 
                'coeff_d' => 0.0, 
                'coeff_e' => 0.2667911, 
                'coeff_f' => -196654.847,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('name', 'Mapa warstwicowa')->first()->id, 
                'page' => 1, 
                'coeff_a' => 0.2838567, 
                'coeff_b' => 0.0, 
                'coeff_c' => -131909.927, 
                'coeff_d' => 0.0, 
                'coeff_e' => 0.2837924, 
                'coeff_f' => -209670.087,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('name', 'Mapa warstwicowa')->first()->id, 
                'page' => 2, 
                'coeff_a' => 0.2670136, 
                'coeff_b' => 0.0, 
                'coeff_c' => -124458.643, 
                'coeff_d' => 0.0, 
                'coeff_e' => 0.2667911, 
                'coeff_f' => -196654.847,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('name', 'Mapa warstwicowa z infrastrukturą')->first()->id, 
                'page' => 1, 
                'coeff_a' => 0.2838567, 
                'coeff_b' => 0.0, 
                'coeff_c' => -131909.927, 
                'coeff_d' => 0.0, 
                'coeff_e' => 0.2837924, 
                'coeff_f' => -209670.087,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('name', 'Mapa warstwicowa z infrastrukturą')->first()->id, 
                'page' => 2, 
                'coeff_a' => 0.2670136, 
                'coeff_b' => 0.0, 
                'coeff_c' => -124458.643, 
                'coeff_d' => 0.0, 
                'coeff_e' => 0.2667911, 
                'coeff_f' => -196654.847,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('name', 'Mapa hipsometryczna (NMT)')->first()->id, 
                'page' => 1, 
                'coeff_a' => 0.2838567, 
                'coeff_b' => 0.0, 
                'coeff_c' => -131909.927, 
                'coeff_d' => 0.0, 
                'coeff_e' => 0.2837924, 
                'coeff_f' => -209670.087,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('name', 'Mapa hipsometryczna (NMT)')->first()->id, 
                'page' => 2, 
                'coeff_a' => 0.2670136, 
                'coeff_b' => 0.0, 
                'coeff_c' => -124458.643, 
                'coeff_d' => 0.0, 
                'coeff_e' => 0.2667911, 
                'coeff_f' => -196654.847,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('name', 'Mapa historyczna')->first()->id, 
                'page' => 1, 
                'coeff_a' => 0.2838567, 
                'coeff_b' => 0.0, 
                'coeff_c' => -131909.927, 
                'coeff_d' => 0.0, 
                'coeff_e' => 0.2837924, 
                'coeff_f' => -209670.087,
            ]);

            MapPage::create([
                'map_file_id' => MapFile::where('name', 'Mapa historyczna')->first()->id, 
                'page' => 2, 
                'coeff_a' => 0.2670136, 
                'coeff_b' => 0.0, 
                'coeff_c' => -124458.643, 
                'coeff_d' => 0.0, 
                'coeff_e' => 0.2667911, 
                'coeff_f' => -196654.847,
            ]);


        }

        
    }
}
