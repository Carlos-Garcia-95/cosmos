<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeliculaTable extends Migration
{
    public function up()
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
            /* $table->foreignId('id_edad_recomendada')->nullable()->constrained('edad_recomendada'); */
            /* $table->foreignId('id_sala')->nullable()->constrained('sala'); */
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pelicula');
    }
}
