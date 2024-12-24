<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 
        'description', 
        'easting', 
        'northing', 
        'pointVirtual', 
        'url',
    ];

    public static function rules()
    {
        return [
            'code' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'easting' => 'required|numeric',
            'northing' => 'required|numeric',
            'pointVirtual' => 'required|boolean',
            'url' => 'nullable|url',
        ];
    }
    // ggh todo: zwrot obszaru z całym obiektem

    
    public function paths(): BelongsToMany
    {
        return $this
            ->belongsToMany(Path::class, 'path_point')
            ->withPivot('position')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this
            ->belongsToMany(Tag::class, 'point_tag')
            ->withTimestamps();
    }

    public function areas(): BelongsToMany
    {
        return $this
            ->belongsToMany(Area::class, 'area_point')
            ->withTimestamps();
    }
}
