<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'area_id'
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
            'area_id' => 'required|numeric',
        ];
    }
    
    public function paths(): BelongsToMany
    {
        return $this
            ->belongsToMany(Path::class, 'paths_points')
            ->withPivot('position')
            ->withTimestamps();
    }

    public function pointTags(): BelongsToMany
    {
        return $this
            ->belongsToMany(PointTag::class, 'points_point_tags')
            ->withTimestamps();
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

}
