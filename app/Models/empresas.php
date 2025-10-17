<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class empresas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nit',
        'nombreEmpresa',
        'direccion',
        'ciudad',
        'telefono'
    ];

    protected $table = 'empresas';

    public function representante(): HasOne
    {
        return $this->hasOne(representantes::class);
    }

    public function maquinas(): HasMany
    {
        return $this->hasMany(Maquinas::class, 'empresa_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(pagos::class);
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(solicitudes::class);
    }
}