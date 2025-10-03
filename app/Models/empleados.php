<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class empleados extends Model
{
    use HasFactory;

    protected $fillable = [
        'Documento',
        'Nombre',
        'Apellido',
        'Telefono',
        'Email',
        'cargos_id'
    ];

    protected $table = 'empleados';

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(cargos::class);
    }

    public function solicitudes(): BelongsToMany
    {
        return $this->belongsToMany(solicitudes::class, 'solicitud_empleado', 'empleado_id', 'solicitud_id')
                    ->withTimestamps();
    }
}