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
        Schema::create('pelicula', function (Blueprint $table) {
            $table->id('id');
            $table->boolean('adult');
            $table->string('backdrop_ruta');
            $table->unsignedBigInteger('id_api');
            $table->string('lenguaje_original');
            $table->string('titulo_original');
            $table->text('sinopsis');
            $table->string('poster_ruta');
            $table->string('fecha_estreno');
            $table->string('titulo');
            $table->boolean('video');
            $table->boolean('activa');
            $table->timestamp('created_at')->useCurrent();
            
            $table->unsignedBigInteger('id_sala')->nullable();              // id sala
            $table->foreign('id_sala')->references('id_sala')->on('sala');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelicula');
    }
};
