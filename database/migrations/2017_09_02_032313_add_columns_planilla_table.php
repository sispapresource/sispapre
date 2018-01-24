<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsPlanillaTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        //
        Schema::table('planilla', function (Blueprint $table) {
            $table->integer('cantidad_horas')->default(0);
            $table->integer('precio_unitario')->default(0);
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
        Schema::table('planilla', function (Blueprint $table) {
            $table->dropColumn(['cantidad_horas', 'precio_unitario']);
        });
    }
}
