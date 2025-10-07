<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitudes extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigoSolicitud',
        'fechaSolicitud',
        'fechaProgramada',
        'descripcion',
        'cantidadMaquinas',
        'fotos',
        'empresas_id',
        'user_id',
        'fecha_solicitud',
        'fecha_uso',
        'hora_inicio',
        'hora_fin',
        'proyecto',
        'lugar',
        'estado'
    ];

    protected $casts = [
        'fechaSolicitud' => 'date',
        'fechaProgramada' => 'date',
        'fecha_solicitud' => 'date',
        'fecha_uso' => 'date',
        'fotos' => 'array',
    ];

    // Relación con User - ESTA ESTÁ BIEN
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con Empresa
    public function empresa()
    {
        return $this->belongsTo(empresas::class, 'empresas_id');
    }

    // RELACIÓN CORREGIDA - Cambiar 'maquinarias' por 'maquinas'
    public function maquinas()
    {
        return $this->belongsToMany(Maquinas::class, 'solicitud_maquina')
                    ->withPivot('cantidad', 'created_at', 'updated_at')
                    ->withTimestamps();
    }

    // Relación con empleados
    public function empleados()
    {
        return $this->belongsToMany(empleados::class, 'solicitud_empleado')
                    ->withTimestamps();
    }
}