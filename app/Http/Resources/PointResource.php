<?php

namespace App\Http\Resources;

//use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// klasa używana do formatowania danych w odpowiedzi API
// klasa punktu, który jest elementem trasy
// posiada dodatkowe pole 'position', czyli pozycję natrasie
class PointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'easting' => $this->easting,
            'northing' => $this->northing,
            'pointVirtual' => $this->pointVirtual,
            'tags' => $this->pointTags->map(function ($tag) { 
                return [
                    'id' => $tag->id,
                    'name' => $tag->tag, 
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
