<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePresupuestoYAvancePorcentajes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('presupuesto_y_avance', function (Blueprint $table) {
            $table->decimal('porcentaje_teorico', 5,2)->change();
            $table->decimal('porcentaje_avance', 5,2)->change();
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
        Schema::table('presupuesto_y_avance', function (Blueprint $table) {
            $table->decimal('porcentaje_teorico', 4,2)->change();
            $table->decimal('porcentaje_avance', 4,2)->change();    
        });
    }
}
