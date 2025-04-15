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
            $table->id('id_user');
            $table->string('nombre_user', 50); //Lo quitamos??
            $table->string('nombre', 50);
            $table->string('apellidos', 50);
            $table->string('email', 50)->unique();
            $table->date('fecha_nacimiento');
            $table->string('numero_telefono', 15);
            $table->string('dni', 30)->unique();
            $table->string('direccion', 150)->nullable();
            $table->string('ciudad', 30);
            $table->string('codigo_postal', 10);
            $table->string('password', 200);
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
        Schema::dropIfExists('users');
    }
};