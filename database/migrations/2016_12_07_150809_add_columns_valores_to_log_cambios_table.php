<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsValoresToLogCambiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_cambios', function($table) {
            $table->decimal('valor_anterior', 11, 2);
            $table->decimal('valor_nuevo', 11, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_cambios', function($table) {
            $table->decimal('valor_anterior', 11, 2);
            $table->decimal('valor_nuevo', 11, 2);
        });
    }
}
