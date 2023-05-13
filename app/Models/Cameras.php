<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cameras extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id_cam_seguretat';

    public $timestamps = false;

    protected $fillable = [
        'codi_cam_seguretat',
        'tipus_cam_seguretat',
        'num_cam_seguretat',
        'codi_suport',
        'codi_districte',
        'nom_districte',
        'codi_barri',
        'nom_barri',
        'X_ETRS89',
        'Y_ETRS89',
        'longitud',
        'latitud',
        'data_alta',
    ];
}


