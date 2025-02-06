<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// klasa używana do formatowania danych w odpowiedzi API
// klasa punktu, który jest elementem trasy
// nie posiada pola 'position', pozycja nadawana jest wewnątrz klasy GeneratorServer
class GeneratorPointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'easting' => $this->easting,
            'northing' => $this->northing,
            'pointVirtual' => $this->pointVirtual,
            'areas' => $this->areas->map(function ($area) { 
                return [
                    'id' => $area->id,
                    'name' => $area->name,
                ];
            }),
            // 'position' => $this->pivot->position
        ];
    }
}
