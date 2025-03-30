<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asiento extends Model
{
    /** @use HasFactory<\Database\Factories\AsientoFactory> */
    use HasFactory;

    public function tipo_asiento() {
        return $this->belongsTo(Tipo_asiento::class,, 'id_tipo_asiento');
    }
}
