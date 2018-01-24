<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsFacturaTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        //
        Schema::table('facturas', function (Blueprint $table) {
            $table->integer('id_propuesta_items')->references('id')->on('propuesta_items')->nullable();
            $table->integer('unidad')->default(0);
            $table->integer('cantidad')->default(0);
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
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['id_propuesta_items', 'unidad', 'cantidad']);
        });
    }
}
