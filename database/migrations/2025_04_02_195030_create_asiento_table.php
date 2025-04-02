<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsientoTable extends Migration
{
    public function up()
    {
        Schema::create('asiento', function (Blueprint $table) {
            $table->id('id_asiento');
            $table->integer('estado');
            /* $table->foreignId('id_sala')->constrained('sala');
            $table->foreignId('id_tipo_asiento')->constrained('tipo_asiento'); */
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asiento');
    }
}
