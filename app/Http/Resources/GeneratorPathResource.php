<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// klasa używana do formatowania danych w odpowiedzi API
// klasa trasy stworzonej Generatorze Tras
class GeneratorPathResource extends JsonResource
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
            'points' => GeneratorPointResource::collection($this->points)
        ];
    }
}
