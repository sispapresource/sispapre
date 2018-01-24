<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdendasDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adendas_detalle', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_adenda')->references('id')->on('adendas');
            $table->string('id_cuenta', 45)->references('id_cuenta')->on('cuentas');
            $table->decimal('monto_anterior', 11, 2);
            $table->decimal('monto_nuevo', 11, 2);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('adendas_detalle');
    }
}
