<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoProyectoDocumentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('documento_tipo_proyecto', function(Blueprint $table)
                       {
                           $table->integer('tipo_proyecto_id')->unsigned()->nullable();
                           $table->foreign('tipo_proyecto_id')->references('id')
                               ->on('tipoProyecto')->onDelete('cascade');

                           $table->integer('documento_id')->unsigned()->nullable();
                           $table->foreign('documento_id')->references('id')
                               ->on('documentos')->onDelete('cascade');
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
        Schema::drop('documento_tipo_proyecto');
    }
}
