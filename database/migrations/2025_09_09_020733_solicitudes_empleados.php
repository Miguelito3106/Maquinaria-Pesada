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
        if (!Schema::hasTable('solicitudes')) {
            Schema::create('solicitudes', function (Blueprint $table) {
                $table->id();

                // Código único de solicitud
                $table->string('codigo', 20)->unique();

                // Relación con la maquinaria (una o varias)
                $table->foreignId('maquinaria_id')->constrained('maquinarias')->onDelete('cascade');

                // Relación con el tipo de mantenimiento (si existe tabla mantenimientos)
                $table->foreignId('mantenimiento_id')->nullable()->constrained('mantenimientos')->onDelete('set null');

                // Cantidad de máquinas solicitadas
                $table->integer('cantidad')->default(1);

                // Fecha deseada para el mantenimiento
                $table->date('fecha_deseada');

                // Descripción detallada
                $table->text('descripcion')->nullable();

                // Fotografía o evidencia del mantenimiento deseado
                $table->string('foto')->nullable();

                // Estado de la solicitud (pendiente, en proceso, completada, etc.)
                $table->string('estado')->default('pendiente');

                $table->timestamps();
            });
        }

        // Tabla pivote entre solicitud y empleados (asignación del personal)
        if (!Schema::hasTable('solicitud_empleado')) {
            Schema::create('solicitud_empleado', function (Blueprint $table) {
                $table->id();
                $table->foreignId('solicitud_id')->constrained('solicitudes')->onDelete('cascade');
                $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_empleado');
        Schema::dropIfExists('solicitudes');
    }
};
