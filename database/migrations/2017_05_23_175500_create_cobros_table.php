<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCobrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cobros', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_cobro', 45);
            $table->timestamp('fecha')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('id_centro')->references('id_centro')->on('centros_contables');
            $table->string('num_fact', 45);
            $table->decimal('monto', 11, 2);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cobros');
    }
}
