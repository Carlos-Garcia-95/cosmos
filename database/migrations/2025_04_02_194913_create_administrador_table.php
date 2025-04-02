<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdministradorTable extends Migration
{
    public function up()
    {
        Schema::create('administrador', function (Blueprint $table) {
            $table->id('id_administrador');
            $table->string('nombre_usuario_admin', 30)->unique();
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('email', 50)->unique();
            $table->date('fecha_nacimiento');
            $table->string('numero_telefono', 15);
            $table->string('contrasena', 50);
            $table->string('codigo_administrador', 20)->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('administrador');
    }
}