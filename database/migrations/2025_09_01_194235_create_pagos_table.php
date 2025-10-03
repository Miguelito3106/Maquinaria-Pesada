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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->string('codigoPago')->unique();
            $table->date('fechaPago');
            $table->decimal('monto', 10, 2);
            $table->enum('metodoPago', ['efectivo', 'tarjeta', 'transferencia']);
            $table->string('referencia')->nullable();
            $table->enum('estado', ['pendiente', 'completado', 'rechazado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->foreignId('mantenimientos_id')->constrained('mantenimientos')->onDelete('cascade');
            $table->foreignId('empresas_id')->constrained('empresas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};