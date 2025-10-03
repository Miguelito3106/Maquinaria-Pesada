<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Maquinas extends Model
{
    use HasFactory;

    protected $fillable = [
        'TipoMaquina',
        'categorias_maquinarias_id'
    ];

    protected $table = 'maquinas';

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(categoriasMaquinarias::class);
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimientos::class);
    }

    public function solicitudes(): BelongsToMany
    {
        return $this->belongsToMany(solicitudes::class, 'solicitud_maquina')
                    ->withPivot(['mantenimientos_id', 'cantidad'])
                    ->withTimestamps();
    }

    // NUEVA: Relación con mantenimientos a través de la tabla pivote
    public function mantenimientosPivot(): BelongsToMany
    {
        return $this->belongsToMany(Mantenimientos::class, 'solicitud_maquina', 'maquinas_id', 'mantenimientos_id')
                    ->withPivot(['solicitud_id', 'cantidad'])
                    ->withTimestamps();
    }
}