<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
    public function points(){
        return $this
            ->belongsToMany(Point::class, 'point_tag')
            ->withTimestamps();
    }
}