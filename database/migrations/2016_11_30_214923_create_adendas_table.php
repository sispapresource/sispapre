<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adendas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_centro')->references('id_centro')->on('centros_contables');
            $table->integer('id_usuario')->references('id')->on('users');
            //$table->dateTime('fecha_transaccion')->default(DB::raw('CURRENT_TIMESTAMP')); //other version mysql
            $table->timestamp('fecha_transaccion')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->string('observaciones')->nullable();
            $table->string('referencia')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('adendas');
    }
}

    
