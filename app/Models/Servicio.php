<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $primaryKey = 'id_servicio';

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'precio' => 'decimal:2'];
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_servicio');
    }
}
