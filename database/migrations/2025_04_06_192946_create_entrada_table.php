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
        Schema::create('entrada', function (Blueprint $table) {
            $table->id('id_entrada');
            $table->string('codigo_qr', 50);
            $table->integer('cantidad');
            $table->timestamp('fecha_compra')->useCurrent();
            $table->unsignedBigInteger('id_tipo_entrada');
            $table->double('monto_total_tipo_entrada');
            $table->unsignedBigInteger('id_factura');
            $table->unsignedBigInteger('id_pelicula');
            $table->unsignedBigInteger('id_asiento');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_tipo_entrada')->references('id_tipo_entrada')->on('tipo_entrada');
            $table->foreign('id_factura')->references('id_factura')->on('factura');
            $table->foreign('id_pelicula')->references('id_pelicula')->on('pelicula');
            $table->foreign('id_asiento')->references('id_asiento')->on('asiento');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrada');
    }
};
