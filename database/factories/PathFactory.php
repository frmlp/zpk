<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Point; 
use App\Models\Path;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Path>
 */
class PathFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence, 
        ];
    }

    public function withPoints(int $count = 3): Factory
    {
        return $this->afterCreating(function (Path $path) use ($count) {
            $points = Point::factory()->count($count)->create(); 
            $positions = range(0, $count - 1);
            shuffle($positions); 

            foreach ($positions as $index => $position) {
                $path->points()->attach($points[$index], ['position' => $position]);
            }
        });
    }
}
