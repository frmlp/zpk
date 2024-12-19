<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Path extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function points(): BelongsToMany{
        return $this
            ->belongsToMany(Point::class, 'paths_points')
            ->withPivot('position')
            ->orderBy('paths_points.position')
            ->withTimestamps();
    }

    /* ggh: jeżeli będzie potrzebne tagowanie tras:
        public function tags(){
            return $this
                ->belongsToMany(PathTag::class, 'paths_path_tags')
                ->withTimestamps();
        }
    */
}
// ggh todo: co ma się zadziać jak usunę punkt co ma się dziać z trasami które je uwzględniały
// a trzeba utrzymać punkty przy usuwaniu tras