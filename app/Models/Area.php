<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_easting', 
        'max_easting',
        'min_northing',
        'max_northing'

    ];

    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'min_easting' => 'required|numeric',
            'max_easting' => 'required|numeric',
            'min_northing' => 'required|numeric',
            'max_northing' => 'required|numeric'
        ];
    }

    public function points(): BelongsToMany
    {
        return $this->belongsToMany(Point::class, 'area_point');
    }
}
