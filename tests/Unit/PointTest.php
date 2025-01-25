<?php

namespace Tests\Unit;

use App\Models\Point;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PointTest extends TestCase
{

    use RefreshDatabase;

    // TEST 1
    public function test_tworzenie_punktu_z_poprawnymi_danymi()
    {
        $data = [
            'code' => 'TEST123',
            'description' => 'Punkt testowy',
            'easting' => 123456,
            'northing' => 789012,
            'pointVirtual' => false,
            'url' => 'https://example.com',
        ];

        $point = Point::create($data);

        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals($data['code'], $point->code);
        $this->assertEquals($data['description'], $point->description);
        $this->assertEquals($data['easting'], $point->easting);
        $this->assertEquals($data['northing'], $point->northing);
        $this->assertEquals($data['pointVirtual'], $point->pointVirtual);
        $this->assertEquals($data['url'], $point->url);
    }

    // TEST 2
    public function test_tworzenie_punktu_z_niepoprawnymi_danymi()
    {
        $user = User::factory()->create();
        $this->actingAs($user); 

        $data = [
            'code' => 5, 
            'description' => 'Punkt testowy',
            'easting' => 'bledna wartosc', 
            'northing' => 789012,
            'pointVirtual' => false,
            'url' => 'https://example.com',
        ];

        $response = $this->post(route('admin.points.store'), $data);

        $response->assertStatus(422); 
        $response->assertJsonValidationErrors(['code', 'easting']); 

    }
}
