<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
>>>>>>> c68366e91dc44810792a8e4350ad7a48bd2b1495

class Pelicula extends Model
{
    use HasFactory;

    protected $table = 'pelicula';
<<<<<<< HEAD

    protected $fillable = [
        'tmdb_id',
        'adult',
        'backdrop_path',
        'genre_ids',
        'original_language',
        'original_title',
        'overview',
        'popularity',
        'poster_path',
        'release_date',
        'title',
        'video',
        'vote_average',
        'vote_count',
        'activo',
=======
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
>>>>>>> c68366e91dc44810792a8e4350ad7a48bd2b1495
    ];

    protected $casts = [
        'adult' => 'boolean',
<<<<<<< HEAD
        'genre_ids' => 'json',
        'popularity' => 'float',
        'video' => 'boolean',
        'vote_average' => 'float',
        'vote_count' => 'integer',
        'activo' => 'boolean',
    ];

}

=======
        'video' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function generos(): BelongsToMany
    {
        return $this->belongsToMany(GeneroPelicula::class, 'pelicula_genero', 'id_pelicula', 'id_genero_pelicula', 'id', 'id_genero_pelicula')
                    ->withTimestamps();
    }
}
>>>>>>> c68366e91dc44810792a8e4350ad7a48bd2b1495
