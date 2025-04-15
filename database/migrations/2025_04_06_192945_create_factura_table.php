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
        Schema::create('factura', function (Blueprint $table) {
            $table->id('id_factura');
            $table->unsignedBigInteger('id_usuario');
            $table->double('monto_total');
            $table->unsignedBigInteger('id_impuesto');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario');
            $table->foreign('id_impuesto')->references('id_impuesto')->on('impuesto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura');
    }
};
