<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'factura';
    protected $primaryKey = 'id_factura';
    public $timestamps = true;

    protected $fillable = [
        'monto_total',
        'ultimos_digitos',
        'titular',
        'id_user',
        'id_impuesto',
    ];

    // Relación a tabla user
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    // Relación a tabla impuesto
    public function impuesto()
    {
        return $this->belongsTo(Impuesto::class, 'id_impuesto', 'id_impuesto');
    }
}
