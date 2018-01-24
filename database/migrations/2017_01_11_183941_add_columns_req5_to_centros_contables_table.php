<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsReq5ToCentrosContablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('centros_contables', function($table) {
            $table->string('contratante', 100);
            $table->string('tel_contratante', 45);
            $table->decimal('monto_contratado', 11, 2);
            $table->string('tipo', 45);
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
            $table->string('contratante', 100);
            $table->string('tel_contratante', 45);
            $table->decimal('monto_contratado', 11, 2);
            $table->string('tipo', 45);
        });
    }
}
