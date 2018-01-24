<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleVersionPropuestaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_version_propuesta', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_version')->unsigned()->nullable();
            $table->foreign('id_version')->references('id')->on('versiones_propuesta');
            $table->integer('id_categoria')->unsigned()->nullable();
            $table->foreign('id_categoria')->references('id')->on('propuesta_categorias');
            $table->integer('losa');
            $table->integer('id_item')->unsigned()->nullable();
            $table->foreign('id_item')->references('id')->on('propuesta_items');
            $table->integer('cantidad');
            $table->integer('id_unidad')->unsigned()->nullable();
            $table->foreign('id_unidad')->references('id')->on('item_unidades');
            $table->decimal('precio_unitario', 11, 2);
            $table->integer('total');
            $table->decimal('porcentaje_total', 4, 2);
            $table->string('id_cuenta')->references('id_cuenta')->on('cuentas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('detalles_version_propuesta');
    }
}
