<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdendasFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adendas_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_file');
            $table->integer('id_adenda')->references('id')->on('adendas');
            $table->string('url');
            $table->string('name_user');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('adendas_files');
    }
}
