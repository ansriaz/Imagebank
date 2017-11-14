<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Imageurls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imageurls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('imageid');
            $table->string('filename');
            $table->text('original')->nullable();
            $table->text('ssmall')->nullable();
            $table->text('qlarge')->nullable();
            $table->text('thumbnail')->nullable();
            $table->text('msmall')->nullable();
            $table->text('nsmall')->nullable();
            $table->text('medium')->nullable();
            $table->text('zmediun')->nullable();
            $table->text('cmedium')->nullable();
            $table->text('blarge')->nullable();
            $table->text('hlarge')->nullable();
            $table->text('klarge')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

            // s   small square 75x75
            // q   large square 150x150
            // t   thumbnail, 100 on longest side
            // m   small, 240 on longest side
            // n   small, 320 on longest side
            // -   medium, 500 on longest side
            // z   medium 640, 640 on longest side
            // c   medium 800, 800 on longest side†
            // b   large, 1024 on longest side*
            // h   large 1600, 1600 on longest side†
            // k   large 2048, 2048 on longest side
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('imageurls');
    }
}
