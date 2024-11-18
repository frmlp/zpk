<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// klasa używana do formatowania danych w odpowiedzi API
// klasa trasy pobranej z bazy danych i przekazanej na front do Bazy Tras
class PathResource extends JsonResource
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
            'name' => $this->name,
            'points' => PointResource::collection($this->points),
            
        ];
    }
}
