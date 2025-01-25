<?php

namespace Tests\Feature;

use App\Models\Path;
use App\Models\Point;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_zwrócenie_wszystkich_punktów()
    {
        // Utwórz kilka punktów
        Point::factory()->count(3)->create();

        // Wyślij żądanie GET do endpointu index
        $response = $this->get(route('admin.points.index'));

        // Sprawdź, czy odpowiedź ma status 200
        $response->assertStatus(200);

        // Sprawdź, czy odpowiedź zawiera wszystkie punkty
        $response->assertJsonCount(3, 'data');
    }

    public function test_zwrócenie_jednego_punktu()
    {
        // Utwórz punkt
        $point = Point::factory()->create();

        // Wyślij żądanie GET do endpointu show
        $response = $this->get(route('admin.points.show', $point));

        // Sprawdź, czy odpowiedź ma status 200
        $response->assertStatus(200);

        // Sprawdź, czy odpowiedź zawiera dane punktu
        $response->assertJsonPath('data.code', $point->code);
    }

    public function test_zwrócenie_wszystkich_ścieżek()
    {
        // Utwórz kilka ścieżek
        Path::factory()->count(3)->create();

        // Wyślij żądanie GET do endpointu index
        $response = $this->get(route('admin.paths.index'));

        // Sprawdź, czy odpowiedź ma status 200
        $response->assertStatus(200);

        // Sprawdź, czy odpowiedź zawiera wszystkie ścieżki
        $response->assertJsonCount(3, 'data');
    }

    public function test_zwrócenie_ścieżek_bez_punktów_wirtualnych()
    {
        // Utwórz punkty - 2 wirtualne i 3 zwykłe
        $virtualPoints = Point::factory()->count(2)->create(['pointVirtual' => true]);
        $nonVirtualPoints = Point::factory()->count(3)->create(['pointVirtual' => false]);

        // Utwórz ścieżki
        $path1 = Path::factory()->create();
        $path2 = Path::factory()->create();
        $path3 = Path::factory()->create();

        // Przypisz punkty do ścieżek
        
        // ścieżka z punktami wirtualnymi
        foreach ($virtualPoints as $index => $point) {
            $path1->points()->attach($point->id, ['position' => $index]);
        } 

        // ścieżka z punktami zwykłymi
        foreach ($nonVirtualPoints as $index => $point) {
            $path2->points()->attach($point->id, ['position' => $index]);
        } 

        // ścieżka z punktami wirtualnymi i zwykłymi
        $allPoints = [...$virtualPoints, ...$nonVirtualPoints];
        foreach ($allPoints as $index => $point) {
            $path3->points()->attach($point->id, ['position' => $index]);
        } 

        // Wyślij żądanie GET do endpointu nonVirtualPaths
        $response = $this->get(route('admin.nonVirtualPaths'));

        // Sprawdź, czy odpowiedź ma status 200
        $response->assertStatus(200);

        // Sprawdź, czy odpowiedź zawiera tylko 1 ścieżkę (path2 - bez punktów wirtualnych)
        $response->assertJsonCount(1, 'paths');

        // Dodatkowe asercje sprawdzające, czy zwrócona ścieżka to path2
        $response->assertJsonPath('paths.0.id', $path2->id); 
    }
}