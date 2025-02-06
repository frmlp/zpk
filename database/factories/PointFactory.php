<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Point>
 */
class PointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word(), // Dodaj generowanie wartości dla 'code'
            'description' => $this->faker->sentence(),
            'easting' => $this->faker->randomNumber(),
            'northing' => $this->faker->randomNumber(),
            'pointVirtual' => $this->faker->boolean(),
            'url' => $this->faker->url(),
        ];
    }
}
