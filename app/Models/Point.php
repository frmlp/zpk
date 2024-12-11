<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Point extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'description', 'easting', 'northing', 'pointVirtual'];

    public function paths(): BelongsToMany
    {
        return $this
            ->belongsToMany(Path::class)
            ->withPivot('position')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this
            ->belongsToMany(PointTag::class, 'points_point_tags')
            ->withTimestamps();
    }
}
