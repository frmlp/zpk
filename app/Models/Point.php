<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Point extends Model
{
    use HasFactory;

    public function paths(): BelongsToMany
    {
        return $this
            ->belongsToMany(Path::class)
            ->withPivot('position')
            ->withTimestamps();
    }
}
