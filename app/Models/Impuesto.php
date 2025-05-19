<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Impuesto extends Model
{
    use HasFactory;

    protected $table = 'impuesto';
    protected $primaryKey = 'id_impuesto';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'cantidad',
    ];
}
