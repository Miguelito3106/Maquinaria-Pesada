<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class cargos extends Model
{
    use HasFactory;

    protected $fillable = [
        'NombreCargo',
        'Descripcion'
    ];

    protected $table = 'cargos';

    public function empleados(): HasMany
    {
        return $this->hasMany(empleados::class);
    }
}