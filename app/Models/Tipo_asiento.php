<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipo_asiento extends Model
{
    /** @use HasFactory<\Database\Factories\TipoAsientoFactory> */
    use HasFactory;

    public function asiento() {
        return $this->hasMany(Asiento::class, 'id_tipo_asiento');
    }
}
