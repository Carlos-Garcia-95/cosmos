<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SesionPelicula extends Model
{
    use HasFactory;

    protected $table = 'sesion_pelicula';

    protected $fillable = [
        'id_sala',
        'id_pelicula',
        'hora',
        'fecha',
    ];

    // Relación a tabla hora
    public function hora()
    {
        return $this->belongsTo(Hora::class, 'hora', 'id');
    }

    // Relación a tabla fecha
    public function fecha()
    {
        return $this->belongsTo(Fecha::class, 'fecha', 'id');
    }

}
