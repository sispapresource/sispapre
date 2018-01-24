<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionPropuestaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versiones_propuesta', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('fecha_creacion');
            $table->timestamp('valido_hasta');
            $table->decimal('monto_total', 11, 2);
            $table->integer('id_estado')->unsigned()->nullable();
            $table->foreign('id_estado')->references('id')->on('estados_propuesta');
            $table->string('carta_propuesta');
            $table->integer('id_usuario')->unsigned()->nullable();
            $table->foreign('id_usuario')->references('id')->on('users');
            $table->integer('id_propuesta')->unsigned()->nullable();
            $table->foreign('id_propuesta')->references('id')->on('propuestas');
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
        Schema::drop('versiones_propuesta');
    }
}
