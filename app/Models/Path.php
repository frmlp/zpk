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
            ->belongsToMany(Point::class, 'path_point')
            ->withPivot('position')
            ->orderBy('path_point.position')
            ->withTimestamps();
    }
}
