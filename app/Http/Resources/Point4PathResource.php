<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class Point4PathResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'position' => $this->pivot->position+1,
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'easting' => $this->easting,
            'northing' => $this->northing,
            'pointVirtual' => $this->pointVirtual,
            'url' => $this->url,
            'tags' => $this->tags->map(function ($tag) { 
                return [
                    'id' => $tag->id,
                    'name' => $tag->name, 
                ];
            }),
            'areas' => $this->areas->map(function ($area) { 
                return [
                    'id' => $area->id,
                    'name' => $area->name, 
                ];
            }),
        ];
    }
}
