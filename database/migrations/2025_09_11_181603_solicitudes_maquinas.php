<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_solicitud_maquina_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('solicitud_maquina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained()->onDelete('cascade');
            $table->foreignId('maquina_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad');
            $table->timestamps();
            
            // Para evitar duplicados
            $table->unique(['solicitud_id', 'maquina_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('solicitud_maquina');
    }
};