<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    protected $primaryKey = 'id_vehiculo';

    public $timestamps = false;

    protected $fillable = ['id_cliente', 'marca', 'modelo', 'anio', 'placa'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_vehiculo');
    }
}
