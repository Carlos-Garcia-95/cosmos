<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fecha extends Model
{
    use HasFactory;

    protected $table = 'fecha';

    protected $fillable = [
        'fecha',
    ];
}
