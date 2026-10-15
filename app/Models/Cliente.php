<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $primaryKey = 'id_cliente';

    public $timestamps = false;

    protected $fillable = ['nombre', 'apellido', 'telefono', 'correo'];

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class, 'id_cliente');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_cliente');
    }
}
