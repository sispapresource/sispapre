<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsTreeToCentrosContablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('centros_contables', function($table) {
            $table->integer('nivel');
            $table->integer('id_padre');
            $table->integer('totalizador');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('centros_contables', function($table) {
            $table->integer('nivel');
            $table->integer('id_padre');
            $table->integer('totalizador');
        });
    }
}
