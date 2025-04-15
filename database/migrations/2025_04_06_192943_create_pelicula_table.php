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
            $table->id('id_pelicula');
            $table->string('nombre', 50);
            $table->integer('duracion');
            $table->string('director', 150);
            $table->string('actor', 250);
            $table->text('sinopsis');
            $table->date('fecha_estreno');
            $table->timestamp('fecha_alta')->useCurrent();
            $table->date('fecha_baja')->nullable();
            $table->unsignedBigInteger('id_edad_recomendada')->nullable();
            $table->foreign('id_edad_recomendada')->references('id_edad_recomendada')->on('edad_recomendada');
            $table->unsignedBigInteger('id_sala')->nullable();
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
