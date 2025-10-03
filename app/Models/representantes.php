<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class representantes extends Model
{
    use HasFactory;

    protected $fillable = [
        'Nombre',
        'Cedula',
        'Telefono',
        'Email',
        'empresas_id'
    ];

    protected $table = 'representantes';

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(empresas::class, 'empresas_id');
    }
}