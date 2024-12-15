<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTag extends Model
{
    use HasFactory;

    protected $fillable = ['tag'];

    public static function rules()
    {
        return [
            'tag' => 'required|string|max:255',
        ];
    }
    public function points(){
        return $this
            ->belongsToMany(Point::class, 'points_point_tags')
            ->withTimestamps();
    }
}
