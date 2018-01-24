<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_pago', 45);
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
        Schema::drop('pagos');
    }
}
