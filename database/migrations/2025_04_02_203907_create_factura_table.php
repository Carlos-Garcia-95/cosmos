<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturaTable extends Migration
{
    public function up()
    {
        Schema::create('factura', function (Blueprint $table) {
            $table->id('id_factura');
            /* $table->foreignId('id_usuario')->constrained('usuario'); */
            $table->double('monto_total');
            /* $table->foreignId('id_impuesto')->constrained('impuesto'); */
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('factura');
    }
}
