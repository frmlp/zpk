<?php

namespace Tests\Unit;

use App\Models\Point;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $data = [
            'code' => 5, 
            'description' => 'Punkt testowy',
            'easting' => 'aa', 
            'northing' => 789012,
            'pointVirtual' => false,
            'url' => 'https://example.com',
        ];

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        Point::create($data);
    }
}
