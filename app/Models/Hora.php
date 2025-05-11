<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hora extends Model
{
    use HasFactory;

    protected $table = 'hora';

    protected $fillable = [
        'hora',
    ];
}
