<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sala extends Model
{
    use HasFactory;

    protected $table = 'sala';

    protected $fillable = [
        'numero_asientos',
    ];
}
