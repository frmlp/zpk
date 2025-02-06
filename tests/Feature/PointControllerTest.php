<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Point;
use App\Models\Path;
use App\Models\User;

class PointControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function newPointData(): array
    {
        return [
            'code' => 'TEST123',
            'description' => 'Opis testowy',
            'easting' => 123456,
            'northing' => 654321,
            'pointVirtual' => 0,
        ];
    }

    public function test_utworzenie_punktu()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
    
        $newPointData = $this->newPointData();

        $response = $this->post('admin/points', $newPointData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('points', $newPointData); 
        
    }

    public function test_aktualizacja_punktu()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Utwórz punkt testowy
        $point = Point::factory()->create();

        // Utwórz dane testowe
        $newData = [
            'code' => 'TEST123',
            'description' => 'Opis testowy',
            'easting' => 123456,
            'northing' => 654321,
            'pointVirtual' => 0,
        ];

        // Wyślij żądanie PUT
        $response = $this->put(route('admin.points.update', $point), $newData);

        // Sprawdź odpowiedź
        $response->assertStatus(201);
        $response->assertJson(['message' => 'Zaktualizowano punkt']);
        $this->assertDatabaseHas('points', [
            'id' => $point->id,
            'description' => $newData['description'],
            'code' => $newData['code'],
        ]);
    }

    public function test_usuwanie_punktu()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Utwórz punkt testowy z powiązaniami
        $point = Point::factory()->create();
        $path = Path::factory()->create();
        $path->points()->attach($point->id, ['position' => 0]);

        // Wyślij żądanie DELETE
        $response = $this->delete(route('admin.points.destroy', $point));

        // Sprawdź odpowiedź
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Punkt został usunięty.']);
        $this->assertDatabaseMissing('points', ['id' => $point->id]);
        $this->assertDatabaseMissing('point_tag', ['point_id' => $point->id]);
        $this->assertDatabaseMissing('path_point', ['point_id' => $point->id]);
    }
}