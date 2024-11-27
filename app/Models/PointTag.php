<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTag extends Model
{
    use HasFactory;

    //ggh
    protected $fillable = ['tag'];

    //ggh
    public function points(){
        return $this->belongsToMany(Point::class);
    }
}
