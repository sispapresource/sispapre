<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableTrazabilidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trazabilidades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_centro')->references('id_centro')->on('centros_contables');
            $table->string('id_cuenta')->references('id_cuenta')->on('cuentas');
            $table->integer('id_usuario')->references('id')->on('users');
            $table->dateTime('fecha')->default(\DB::raw('CURRENT_TIMESTAMP')); //other version mysql
            $table->decimal('presupuesto', 11,2)->default(0);
            $table->decimal('old_presupuesto', 11,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trazabilidades');
    }
}
