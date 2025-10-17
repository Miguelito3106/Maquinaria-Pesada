<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Solicitudes extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'codigo_solicitud',
        'descripcion',
        'fecha_mantenimiento',
        'fotos',
    ];

    protected $casts = [
        'fecha_mantenimiento' => 'date',
        'fotos' => 'array',
    ];

    /**
     * Relación: una solicitud puede tener muchas máquinas (con mantenimiento y cantidad).
     * Usa la tabla pivote solicitud_maquina.
     */
    public function maquinas()
    {
        return $this->belongsToMany(Maquinas::class, 'solicitud_maquina', 'solicitud_id', 'maquinas_id')
                    ->withPivot('mantenimientos_id', 'cantidad')
                    ->withTimestamps();
    }

    /**
     * Relación: una solicitud puede incluir diferentes tipos de mantenimiento.
     */
    public function mantenimientos()
    {
        return $this->belongsToMany(Mantenimientos::class, 'solicitud_maquina', 'solicitud_id', 'mantenimientos_id')
                    ->withPivot('maquinas_id', 'cantidad')
                    ->withTimestamps();
    }

    /**
     * Calcula la cantidad total de máquinas involucradas en la solicitud.
     */
    public function getCantidadTotalMaquinasAttribute()
    {
        return $this->maquinas->sum(fn($maquina) => $maquina->pivot->cantidad);
    }

    /**
     * Genera un código de solicitud único con formato SOL-YYYYMMDD-XXXXXX.
     */
    public static function generarCodigo()
    {
        do {
            $codigo = 'SOL-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::where('codigo_solicitud', $codigo)->exists());

        return $codigo;
    }

    /**
     * Evento boot: asigna automáticamente el código de solicitud al crear el registro.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($solicitud) {
            if (empty($solicitud->codigo_solicitud)) {
                $solicitud->codigo_solicitud = self::generarCodigo();
            }
        });
    }
}
