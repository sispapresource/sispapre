<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineaDeVentaCategoriasUnidadItemTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('propuesta_lineas_de_venta', function(Blueprint $table){
            $table->increments('id');
            $table->string('nombre');
        });
        Schema::create('propuesta_categorias', function(Blueprint $table){
            $table->increments('id');
            $table->string('nombre');
        });
        Schema::create('propuesta_items', function(Blueprint $table){
            $table->increments('id');
            $table->string('nombre');
        });
        Schema::create('item_unidades', function(Blueprint $table){
            $table->increments('id');
            $table->string('nombre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('propuesta_lineas_de_venta');
        Schema::drop('propuesta_categorias');
        Schema::drop('propuesta_items');
        Schema::drop('item_unidades');

    }
}
