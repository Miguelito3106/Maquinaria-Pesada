<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class pagos extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigoPago',
        'fechaPago',
        'monto',
        'metodoPago',
        'referencia',
        'estado',
        'observaciones',
        'mantenimientos_id',
        'empresas_id'
    ];

    protected $casts = [
        'fechaPago' => 'date',
        'monto' => 'decimal:2',
    ];

    protected $table = 'pagos';

    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(Mantenimientos::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(empresas::class);
    }
}