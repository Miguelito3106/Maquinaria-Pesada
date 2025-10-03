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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('codigoSolicitud')->unique();
            $table->date('fechaSolicitud');
            $table->date('fechaProgramada'); 
            $table->string('descripcion');
            $table->integer('cantidadMaquinas')->default(1); 
            $table->json('fotos')->required ();
            $table->foreignId('empresas_id')->constrained('empresas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};