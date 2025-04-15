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
            $table->integer('estado');
            $table->unsignedBigInteger('id_sala');
            $table->unsignedBigInteger('id_tipo_asiento');
            $table->foreign('id_sala')->references('id_sala')->on('sala');
            $table->foreign('id_tipo_asiento')->references('id_tipo_asiento')->on('tipo_asiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asiento');
    }
};
