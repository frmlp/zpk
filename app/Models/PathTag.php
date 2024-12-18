<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathTag extends Model
{
    use HasFactory;

    protected $fillable = ['tag'];

    public function paths(){
        return $this->belongsToMany(Path::class, 'paths_path_tags');
    }
}
