<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Imageclasslabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imageclasslabels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('image_id')->unsigned();
            $table->integer('class_id')->unsigned();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('imageclasslabels', function($table) {

            // $table->dropForeign('imageclasslabels_image_id_foreign');
            // $table->dropForeign('imageclasslabels_class_id_foreign');

            // $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade')->change();
            // $table->foreign('class_id')->references('id')->on('classlabel')->onDelete('cascade')->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('imageclasslabels');
    }
}
