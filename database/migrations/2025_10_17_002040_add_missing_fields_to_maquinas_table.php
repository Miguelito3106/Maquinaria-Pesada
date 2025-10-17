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
        Schema::table('maquinas', function (Blueprint $table) {
            $table->string('nombre')->after('TipoMaquina');
            $table->foreignId('empresa_id')->nullable()->after('nombre')->constrained('empresas')->onDelete('set null');
            $table->string('estado')->default('disponible')->after('empresa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maquinas', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropColumn(['nombre', 'empresa_id', 'estado']);
        });
    }
};
