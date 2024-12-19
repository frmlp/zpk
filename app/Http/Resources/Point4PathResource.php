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
            'area_id' => $this->area_id,
            'easting' => $this->easting,
            'northing' => $this->northing,
            'pointVirtual' => $this->pointVirtual,
            'tags' => $this->pointTags->map(function ($tag) { 
                return [
                    'id' => $tag->id,
                    'name' => $tag->tag, 
                ];
            }),
        ];
    }
}
