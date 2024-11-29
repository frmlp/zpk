<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTag extends Model
{
    use HasFactory;

    protected $fillable = ['tag'];

    public function points(){
        return $this->belongsToMany(Point::class);
    }
}
