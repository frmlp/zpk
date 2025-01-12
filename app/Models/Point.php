<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request; 

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 
        'description', 
        'easting', 
        'northing', 
        'pointVirtual', 
        'url',
    ];

    public static function rules()
    {
        return [
            'code' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'easting' => 'required|numeric',
            'northing' => 'required|numeric',
            'pointVirtual' => 'required|boolean',
            'url' => 'nullable|url',
        ];
    }

    
    public function paths(): BelongsToMany
    {
        return $this
            ->belongsToMany(Path::class, 'path_point')
            ->withPivot('position')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this
            ->belongsToMany(Tag::class, 'point_tag')
            ->withTimestamps();
    }

    public function areas(): BelongsToMany
    {
        return $this
            ->belongsToMany(Area::class, 'area_point')
            ->withTimestamps();
    }

    public function assignAreas()
    {
        $areas = Area::all(); 

        foreach ($areas as $area) {
            if ($this->easting >= $area->min_easting && 
                $this->easting <= $area->max_easting && 
                $this->northing >= $area->min_northing && 
                $this->northing <= $area->max_northing) {
                
                $this->areas()->attach($area->id); 
            }
        }
    }

}
