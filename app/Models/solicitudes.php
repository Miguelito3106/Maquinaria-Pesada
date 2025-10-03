<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class solicitudes extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigoSolicitud',
        'fechaSolicitud',
        'fechaProgramada',
        'descripcion',
        'cantidadMaquinas',
        'fotos',
        'empresas_id'
    ];

    protected $table = 'solicitudes';

    protected $casts = [
        'fechaSolicitud' => 'date',
        'fechaProgramada' => 'date',
        'fotos' => 'array',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(empresas::class, 'empresas_id');
    }

    // RELACIÓN DIRECTA con mantenimientos (CORRECTA)
    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimientos::class, 'solicitud_id');
    }

    public function empleados(): BelongsToMany
    {
        return $this->belongsToMany(empleados::class, 'solicitud_empleado', 'solicitud_id', 'empleado_id')
                    ->withTimestamps();
    }

    // RELACIÓN CON MÁQUINAS - SIN USAR TABLA PIVOTE COMPLEJA
    public function maquinas(): BelongsToMany
    {
        return $this->belongsToMany(Maquinas::class, 'solicitud_maquina', 'solicitud_id', 'maquinas_id')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }
}