<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioTable extends Migration
{
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombre_usuario', 50)->unique();
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('email', 50)->unique();
            $table->date('fecha_nacimiento');
            $table->string('numero_telefono', 15);
            $table->string('dni', 30)->unique();
            $table->string('direccion', 150)->nullable();
            $table->string('ciudad', 30);
            $table->string('codigo_postal', 10);
            $table->string('contrasena', 50);
            $table->timestamp('fecha_registro')->useCurrent();
            /* $table->foreignId('id_descuento')->nullable()->constrained('descuento')->onDelete('set null'); */
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario');
    }
}
