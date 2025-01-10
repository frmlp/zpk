<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapFile extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'path', 'icon_path'];

    // Relacja one-to-many: jeden plik mapy ma wiele stron
    public function pages()
    {
        return $this->hasMany(MapPage::class);
    }
}
