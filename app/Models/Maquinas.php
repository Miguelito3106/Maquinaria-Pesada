<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maquinas extends Model
{
    use HasFactory;

    protected $fillable = [
        'TipoMaquina',
        'categorias_maquinarias_id'
    ];

    // RELACIÓN CORREGIDA - Cambiar 'solicitudes_maquinas' por 'solicitudes'
    public function solicitudes()
    {
        return $this->belongsToMany(Solicitudes::class, 'solicitud_maquina')
                    ->withPivot('cantidad', 'created_at', 'updated_at')
                    ->withTimestamps();
    }

    // Relación con categoria
    public function categoria()
    {
        return $this->belongsTo(categoriasMaquinarias::class, 'categorias_maquinarias_id');
    }
}