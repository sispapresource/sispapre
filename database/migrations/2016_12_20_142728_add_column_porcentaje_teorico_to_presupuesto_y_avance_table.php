<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPorcentajeTeoricoToPresupuestoYAvanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('presupuesto_y_avance', function($table) {
            $table->decimal('porcentaje_teorico', 4, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('presupuesto_y_avance', function($table) {
            $table->dropColumn('porcentaje_teorico');
        });
    }
}
