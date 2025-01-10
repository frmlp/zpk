<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapPage extends Model
{
    use HasFactory;

    protected $fillable = ['map_file_id', 'page', 'coeff_a', 'coeff_b', 'coeff_c', 'coeff_d', 'coeff_e', 'coeff_f'];

    // Relacja odwrotna: strona należy do jednego pliku mapy
    public function mapFile()
    {
        return $this->belongsTo(MapFile::class);
    }
}
