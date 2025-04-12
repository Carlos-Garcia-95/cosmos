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
        Schema::create('administrador', function (Blueprint $table) {
            $table->id('id_administrador');
            $table->string('nombre_usuario_admin', 30)->unique();
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('email', 50)->unique();
            $table->date('fecha_nacimiento');
            $table->string('numero_telefono', 15);
            $table->string('contrasenia', 200);
            $table->string('codigo_administrador', 20)->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrador');
    }
};
