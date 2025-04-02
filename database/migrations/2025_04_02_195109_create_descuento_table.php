<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateDescuentoTable extends Migration
{
    public function up()
    {
        Schema::create('descuento', function (Blueprint $table) {
            $table->id('id_descuento');
            $table->integer('descuento')->nullable();
            $table->string('tipo', 30);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('descuento');
    }
}
