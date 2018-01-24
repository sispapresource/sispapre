<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnticiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anticipos', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('fecha')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('id_centro')->references('id_centro')->on('centros_contables');
            $table->string('num_anticipo', 45);
            $table->string('orden_de_compra', 45)->nullable();
            $table->string('subcontrato', 45)->nullable();
            $table->decimal('monto', 11, 2);
            $table->decimal('amortizado', 11, 2);
            $table->decimal('por_amortizar', 11, 2);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('anticipos');
    }
}
