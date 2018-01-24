<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentoProyectoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('centro_contable_documento',function(Blueprint $table){


            $table->integer('documento_id')->unsigned()->nullable();
            $table->foreign('documento_id')->references('id')
                ->on('documentos')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('centro_contable_id')->unsigned()->nullable();
            /*$table->foreign('centro_contable_id')->references('id_centro')
                ->on('centros_contables')->onDelete('cascade');*/
            $table->text('url')->nullable();
            $table->timestamp('fecha_de_carga')->nullable();
            $table->timestamp('fecha_de_expiracion')->nullable();
            $table->boolean('estado')->nullable();
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
        Schema::drop('centro_contable_documento');
    }
}
