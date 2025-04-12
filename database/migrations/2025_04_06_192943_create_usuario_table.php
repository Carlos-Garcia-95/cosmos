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
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('nombre_usuario', 50);
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('email', 50);
            $table->date('fecha_nacimiento');
            $table->string('numero_telefono', 15);
            $table->string('dni', 30);
            $table->string('direccion', 150)->nullable();
            $table->string('ciudad', 30);
            $table->string('codigo_postal', 10);
            $table->string('contrasenia', 200);
            $table->timestamp('fecha_registro')->useCurrent();
            $table->unsignedBigInteger('id_descuento')->nullable();
            $table->foreign('id_descuento')->references('id_descuento')->on('descuento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
