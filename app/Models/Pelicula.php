<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelicula extends Model
{
    use HasFactory;

    protected $table = 'pelicula';

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
    ];

    protected $casts = [
        'adult' => 'boolean',
        'genre_ids' => 'json',
        'popularity' => 'float',
        'video' => 'boolean',
        'vote_average' => 'float',
        'vote_count' => 'integer',
        'activo' => 'boolean',
    ];

}

