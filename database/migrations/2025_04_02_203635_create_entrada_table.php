<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradaTable extends Migration
{
    public function up()
    {
        Schema::create('entrada', function (Blueprint $table) {
            $table->id('id_entrada');
            $table->string('codigo_qr', 50);
            $table->integer('cantidad');
            $table->timestamp('fecha_compra')->useCurrent();
            /* $table->foreignId('id_tipo_entrada')->constrained('tipo_entrada'); */
            $table->double('monto_total_tipo_entrada');
            /* $table->foreignId('id_factura')->constrained('factura'); */
            /* $table->foreignId('id_pelicula')->constrained('pelicula'); */
            /* $table->foreignId('id_asiento')->constrained('asiento'); */
           /*  $table->foreignId('id_usuario')->constrained('usuario')->onDelete('cascade'); */
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entrada');
    }
};
