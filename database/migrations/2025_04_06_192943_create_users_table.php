<?php

/* use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{ */
    /**
     * Run the migrations.
     */
/*     public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    } */

    /**
     * Reverse the migrations.
     */
    /* public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
}; */


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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id'); // Cambié el nombre del ID a id_usuario
            $table->string('nombre', 50);
            $table->string('apellidos', 50);
            $table->string('email', 50)->unique();
            $table->date('fecha_nacimiento');
            $table->string('numero_telefono', 9);
            $table->unsignedBigInteger('ciudad')->nullable(); 
            $table->string('dni', 9)->unique();
            $table->string('direccion', 150)->nullable();
            $table->string('codigo_postal', 10); 
            $table->string('password', 200);
            $table->string('google_id')->nullable();
            $table->rememberToken();
            $table->tinyInteger('mayor_edad')->default(0); 
            $table->unsignedBigInteger('id_descuento')->nullable(); 
            $table->unsignedBigInteger('tipo_usuario')->nullable(); 
            $table->foreign('ciudad')->references('id')->on('ciudades'); 
            $table->foreign('id_descuento')->references('id_descuento')->on('descuento');
            $table->foreign('tipo_usuario')->references('id_tipo_usuario')->on('tipo_usuario');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar la columna google_id
            $table->dropColumn('google_id');
        });
    }
};