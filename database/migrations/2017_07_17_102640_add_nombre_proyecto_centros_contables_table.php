<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNombreProyectoCentrosContablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('centros_contables', function (Blueprint $table) {
            $table->string('nombre_proyecto');
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
        Schema::table('centros_contables', function (Blueprint $table) {
            $table->dropColumn('nombre_proyecto');
        });
    }
}
