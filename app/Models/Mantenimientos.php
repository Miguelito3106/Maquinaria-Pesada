<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mantenimientos extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'costo',
        'tiempoEstimado',
        'manualProcedimiento',
        'fechaEntrega',
        'maquinas_id',
    ];

    protected $table = 'mantenimientos';

    protected $casts = [
        'costo' => 'decimal:2',
        'tiempoEstimado' => 'integer',
        'fechaEntrega' => 'date',
    ];

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquinas::class, 'maquinas_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(pagos::class, 'mantenimientos_id');
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(solicitudes::class, 'solicitud_id');
    }


}