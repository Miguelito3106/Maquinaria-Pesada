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
        Schema::table('solicitud_maquina', function (Blueprint $table) {
            $table->foreignId('mantenimientos_id')->nullable()->constrained('mantenimientos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_maquina', function (Blueprint $table) {
            $table->dropForeign(['mantenimientos_id']);
            $table->dropColumn('mantenimientos_id');
        });
    }
};
