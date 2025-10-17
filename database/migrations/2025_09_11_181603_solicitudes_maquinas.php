<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitud_maquina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes')->onDelete('cascade');
            $table->foreignId('maquinas_id')->constrained('maquinas')->onDelete('cascade');
            $table->foreignId('mantenimientos_id')->constrained('mantenimientos')->onDelete('cascade');
            $table->integer('cantidad')->default(1);
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['solicitud_id', 'maquinas_id', 'mantenimientos_id'], 'solicitud_maquina_mantenimiento_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_maquina');
    }
};