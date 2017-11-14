<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Classlabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('classlabel', function (Blueprint $table) {
        $table->increments('id');
        $table->string('title');
        $table->text('description')->nullable();
        $table->integer('dataset_id')->unsigned();

        $table->rememberToken();
        $table->timestamps();
      });

      Schema::table('classlabel', function (Blueprint $table) {
        // $table->foreign('dataset_id')->references('id')->on('user_datasets')->onDelete('cascade')->change();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('classlabel');
    }
}