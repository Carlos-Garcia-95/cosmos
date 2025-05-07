<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pelicula extends Model
{
    use HasFactory;

    protected $table = 'pelicula';
    protected $primaryKey = 'id';
    public $timestamps = false; // Since you have 'creacion'

    protected $fillable = [
        'adult',
        'backdrop_ruta',
        'id_api',
        'lenguaje_original',
        'titulo_original',
        'sinopsis',
        'poster_ruta',
        'fecha_estreno',
        'titulo',
        'video',
        'id_sala',
    ];

    protected $casts = [
        'adult' => 'boolean',
        'video' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function generos(): BelongsToMany
    {
        return $this->belongsToMany(GeneroPelicula::class, 'pelicula_genero', 'id_pelicula', 'id_genero_pelicula', 'id', 'id_genero_pelicula')
                    ->withTimestamps();
    }
}
