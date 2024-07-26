<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Path extends Model
{
    use HasFactory;

    public function points(): BelongsToMany{
        return $this
            ->belongsToMany(Point::class)
            ->withPivot('position')
            ->withTimestamps();
    }
}
