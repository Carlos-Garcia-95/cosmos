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
        Schema::create('asiento', function (Blueprint $table) {
            $table->id('id_asiento');
            $table->unsignedSmallInteger('estado');
            $table->unsignedBigInteger('id_sala');
            $table->foreignId('id_tipo_asiento')->constrained('tipo_asiento')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asientos');
    }
};
