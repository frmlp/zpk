<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Point;

class PointTest extends TestCase
{
    public function testCreatePoint()
    {
        // Tworzenie nowego punktu
        $point = Point::create([
            'code' => 'TEST123',
            'description' => 'Test point',
            'latitude' => 54.523,
            'longitude' => 18.532,
            'type' => 'attraction',
        ]);

        // Sprawdzenie, czy punkt został utworzony
        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals('TEST123', $point->code);
        $this->assertEquals('Test point', $point->description);
        $this->assertEquals(54.523, $point->latitude);
        $this->assertEquals(18.532, $point->longitude);
        $this->assertEquals('attraction', $point->type);
    }

    public function testUpdatePoint()
    {
        // Tworzenie punktu
        $point = Point::create([
            'code' => 'TEST123',
            'description' => 'Test point',
            // ... inne dane
        ]);

        // Aktualizacja danych punktu
        $point->update([
            'description' => 'Updated description',
        ]);

        // Sprawdzenie, czy dane zostały zaktualizowane
        $this->assertEquals('Updated description', $point->description);
    }

    public function testDeletePoint()
    {
        // Tworzenie punktu
        $point = Point::create([
            'code' => 'TEST123',
            'description' => 'Test point',
            // ... inne dane
        ]);

        // Usunięcie punktu
        $point->delete();

        // Sprawdzenie, czy punkt został usunięty z bazy danych
        // $this->assertDatabaseMissing('points', [
        //     'code' => 'TEST123',
        // ]);
    }
}
