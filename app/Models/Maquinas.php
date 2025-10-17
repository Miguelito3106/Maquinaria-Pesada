<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maquinas extends Model
{
    use HasFactory;
    protected $table = 'maquinarias';
    protected $fillable = [
        'TipoMaquina',
        'nombre',
        'empresa_id',
        'estado',
        'categorias_maquinarias_id'
        
        

    ];

    // RELACIÓN CORREGIDA - Cambiar 'solicitudes_maquinas' por 'solicitudes'
    public function solicitudes()
    {
        return $this->belongsToMany(Solicitudes::class, 'solicitud_maquina', 'maquina_id', 'solicitud_id')
                    ->withPivot('cantidad', 'created_at', 'updated_at')
                    ->withTimestamps();
    }

    // Relación con categoria
    public function categoria()
    {
        return $this->belongsTo(categoriasMaquinarias::class, 'categorias_maquinarias_id');
    }

    // Relación con empresa
    public function empresa()
    {
        return $this->belongsTo(empresas::class, 'empresa_id');
    }

    // Relación con mantenimientos
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimientos::class, 'maquinas_id');
    }
}