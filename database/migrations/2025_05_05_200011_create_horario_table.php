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
        Schema::create('horario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sala');      // id sala
            $table->unsignedBigInteger('id_pelicula');  // id pelicula
            $table->time('hora');                       // hora de la pelicula
            $table->smallInteger('activo');             // si el horario está activo o no
            $table->timestamp('creacion')->useCurrent();

            $table->foreign('id_sala')->references('id_sala')->on('sala');              // id sala
            //$table->foreign('id_pelicula')->references('id_pelicula')->on('pelicula');  // id pelicula
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario');
    }
};
