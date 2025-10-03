<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitud_maquina', function (Blueprint $table) {
            // Agregar la columna mantenimientos_id
            $table->foreignId('mantenimientos_id')->after('maquinas_id')->constrained('mantenimientos')->onDelete('cascade');
            
            // Eliminar el índice único existente
            $table->dropUnique(['solicitud_id', 'maquinas_id']);
            
            // Crear nuevo índice único con los tres campos
            $table->unique(['solicitud_id', 'maquinas_id', 'mantenimientos_id']);
        });
    }

    public function down(): void
    {
        Schema::table('solicitud_maquina', function (Blueprint $table) {
            $table->dropForeign(['mantenimientos_id']);
            $table->dropColumn('mantenimientos_id');
            
            $table->dropUnique(['solicitud_id', 'maquinas_id', 'mantenimientos_id']);
            
            $table->unique(['solicitud_id', 'maquinas_id']);
        });
    }
};