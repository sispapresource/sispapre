<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePresupuestoItems extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        //
        Schema::create('presupuesto_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_centro')->references('id_centro')->on('centros_contables');
            $table->string('id_cuenta')->references('id_cuenta')->on('cuentas');
            $table->integer('id_propuesta_items')->references('id')->on('propuesta_items');
            $table->integer('id_unidad')->references('id')->on('item_unidades');
            $table->integer('cantidad');
            $table->decimal('PrecioUnitario');
            
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
        Schema::dropIfExists('presupuesto_items');
    }
}
