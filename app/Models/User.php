<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Ciudad;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'password',
        'direccion',
        'ciudad',
        'codigo_postal',
        'numero_telefono',
        'dni',
        'mayor_edad',
        'fecha_nacimiento',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //Guardar el nombre con la primera en mayúscula
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucfirst(strtolower($value));
    }

    //Guardar el apellido con la primera en mayúscula
    public function setApellidosAttribute($value)
    {
        $this->attributes['apellidos'] = ucfirst(strtolower($value));
    }

    //Sacar nombre de Ciudades
    public function city()
    {
        // Indica que un Usuario 'pertenece a' (belongsTo) una Ciudad.
        return $this->belongsTo(Ciudad::class, 'ciudad');
    }


}
