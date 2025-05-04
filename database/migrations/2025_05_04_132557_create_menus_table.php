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
        Schema::create('menus', function (Blueprint $table) {
            $table->id(); // Columna 'id' AUTO_INCREMENT como clave primaria
            $table->string('nombre'); // Columna 'nombre' para el nombre del menú
            $table->text('descripcion')->nullable(); // Columna 'descripcion'
            $table->decimal('precio', 8, 2); // Columna 'precio'
            $table->string('imagen_url')->nullable(); // Columna para la URL de la imagen
            

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
