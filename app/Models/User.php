<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Ciudad;

/**
 * 
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellidos
 * @property string $email
 * @property string $fecha_nacimiento
 * @property string $numero_telefono
 * @property string $dni
 * @property string|null $direccion
 * @property string $ciudad
 * @property string $codigo_postal
 * @property string $password
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $mayor_edad
 * @property int|null $id_descuento
 * @property string|null $remember_token
 * @property-read Ciudad|null $city
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereApellidos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCiudad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCodigoPostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFechaNacimiento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdDescuento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMayorEdad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNumeroTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        'tipo_usuario',
        'id_descuento'
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
        return $this->belongsTo(Ciudad::class, 'ciudad', 'id');
    }

    

    //Helper para verificar si el usuario es un empleado
    public function isEmployee()
    {
        return $this->tipo_usuario === 2;
    }

    //Método para definir la relación con las nóminas de empleado
    public function nominas()
    {
        return $this->hasMany(NominaEmpleados::class, 'id_empleado', 'id');
        
    }

    //Si 'tipo_usuario' es un campo numérico y '1' significa admin
    public function isAdmin(): bool
    {
        return $this->tipo_usuario == 1;
    }



}
