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
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nombre'); // NUEVO: Nombre del mantenimiento
            $table->string('descripcion');
            $table->decimal('costo', 10, 2);
            $table->integer('tiempoEstimado'); // NUEVO: Tiempo en horas
            $table->text('manualProcedimiento')->nullable(); // NUEVO: Manual de procedimiento
            $table->date('fechaEntrega');
            $table->foreignId('maquinas_id')->constrained('maquinas')->onDelete('cascade');
            $table->foreignId('solicitud_id')->constrained('solicitudes')->onDelete('cascade');
            $table->timestamps();
            
            // Índices para mejor performance
            $table->index('codigo');
            $table->index('fechaEntrega');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};