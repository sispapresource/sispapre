<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValorConfiguracionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('valor_configuraciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('texto');
            $table->integer('id_configuracion')->unsigned();
            $table->foreign('id_configuracion')->references('id')->on('configuracion_formulas');
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
        Schema::drop('valor_configuraciones');
    }
}
