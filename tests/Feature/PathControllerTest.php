<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Path;
use App\Models\Point;
use App\Models\User;

class PathControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_dodawanie_ścieżki()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Utwórz punkty testowe
        $point1 = Point::factory()->create();
        $point2 = Point::factory()->create();

        $data = [
            'name' => 'Ścieżka testowa',
            'pathType' => 'piesza',
            'points' => [$point1->id, $point2->id],
        ];

        $response = $this->post(route('admin.paths.store'), $data);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Dodano nową ścieżkę']);
        $this->assertDatabaseHas('paths', ['name' => 'Ścieżka testowa']);
        $this->assertDatabaseHas('path_point', ['path_id' => $response->json('path.id'), 'point_id' => $point1->id]);
        $this->assertDatabaseHas('path_point', ['path_id' => $response->json('path.id'), 'point_id' => $point2->id]);
    }

    public function test_aktualizacja_ścieżki()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Utwórz ścieżkę i punkty testowe
        $path = Path::factory()->create();
        $point1 = Point::factory()->create();
        $point2 = Point::factory()->create();
        $point3 = Point::factory()->create();
        $path->points()->attach($point1->id, ['position' => 0]);
        $path->points()->attach($point2->id, ['position' => 1]);

        $newData = [
            'name' => 'Zaktualizowana ścieżka',
            'pathType' => 'rowerowa',
            'points' => [$point3->id, $point1->id], 
        ];

        $response = $this->put(route('admin.paths.update', $path), $newData);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Zaktualizowano ścieżkę']);
        $this->assertDatabaseHas('paths', ['id' => $path->id, 'name' => 'Zaktualizowana ścieżka']);
        $this->assertDatabaseHas('path_point', ['path_id' => $path->id, 'point_id' => $point3->id]);
        $this->assertDatabaseHas('path_point', ['path_id' => $path->id, 'point_id' => $point1->id]);
        $this->assertDatabaseMissing('path_point', ['path_id' => $path->id, 'point_id' => $point2->id]); 
    }

    public function test_usuwanie_ścieżki()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Utwórz ścieżkę i punkt testowy
        $path = Path::factory()->create();
        $point = Point::factory()->create();
        $path->points()->attach($point->id, ['position' => 0]);

        $response = $this->delete(route('admin.paths.destroy', $path));

        $response->assertStatus(204); 
        $this->assertDatabaseMissing('paths', ['id' => $path->id]);
        $this->assertDatabaseMissing('path_point', ['path_id' => $path->id, 'point_id' => $point->id]);
    }
}