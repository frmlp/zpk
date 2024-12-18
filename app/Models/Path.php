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

    public function tags(){
        return $this
            ->belongsToMany(PathTag::class, 'paths_path_tags')
            ->withTimestamps();
    }
}
// ggh todo co ma się zadziać jak usunę punkt co ma się dziać z trasami które je uwzględniały
// a trzeba utrzymać punkty przy usuwaniu tras