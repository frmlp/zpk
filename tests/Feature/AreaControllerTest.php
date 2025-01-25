<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Area;
use App\Models\User;

class AreaControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_dodawanie_obszaru()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'name' => 'Obszar testowy',
            'min_easting' => 1000,
            'max_easting' => 2000,
            'min_northing' => 3000,
            'max_northing' => 4000,
        ];

        $response = $this->post(route('admin.areas.store'), $data);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Dodano nowy obszar']);
        $this->assertDatabaseHas('areas', ['name' => 'Obszar testowy']);
    }

    public function test_aktualizacja_obszaru()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $area = Area::factory()->create();

        $newData = [
            'name' => 'Zaktualizowany obszar',
            'min_easting' => 1500,
            'max_easting' => 2500,
            'min_northing' => 3500,
            'max_northing' => 4500,
        ];

        $response = $this->put(route('admin.areas.update', $area), $newData);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Zaktualizowano obszar']);
        $this->assertDatabaseHas('areas', ['id' => $area->id, 'name' => 'Zaktualizowany obszar']);
    }

    public function test_usuwanie_obszaru()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $area = Area::factory()->create();

        $response = $this->delete(route('admin.areas.destroy', $area));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Obszar został usunięty']);
        $this->assertDatabaseMissing('areas', ['id' => $area->id]);
    }
}