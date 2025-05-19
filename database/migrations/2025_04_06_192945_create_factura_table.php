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
            $table->double('monto_total');
            $table->bigInteger('ultimos_digitos');
            $table->string('titular');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_impuesto');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable(); 
            $table->foreign('id_user')->references('id')->on('users');
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
