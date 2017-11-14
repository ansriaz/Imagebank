<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Images extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('photo_id')->nullable();
            $table->string('filename');
            $table->string('owner');
            $table->string('secret')->nullable();
            $table->string('server')->nullable();
            $table->string('farm')->nullable();
            $table->string('author')->nullable();
            $table->string('title')->nullable();
            $table->string('is_public')->nullable();
            $table->string('license');
            $table->text('link')->nullable();
            $table->string('uri');
            $table->text('tags')->nullable();
            $table->string('source')->nullable();
            $table->datetime('date_uploaded')->nullable();
            $table->string('date_last_update')->nullable();
            $table->string('date_taken');
            $table->string('ownername')->nullable();
            $table->string('views')->nullable();
            $table->string('contributorlocation')->nullable();
            $table->string('accuracy')->nullable();
            $table->text('description')->nullable();
            $table->integer('version')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('dataset_id')->unsigned();
            $table->string('pathalias')->nullable();
            $table->text('machine_tags')->nullable();
            $table->string('place_id')->nullable();
            $table->string('woeid')->nullable();
            $table->string('geo_is_public')->nullable();
            $table->string('media')->nullable();
            $table->string('media_status')->nullable();
            $table->integer('class_id')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('images', function($table) {
            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('dataset_id')->references('id')->on('user_datasets')->onDelete('cascade')->change();
        });

        Schema::table('images', function($table) {
            $table->index('name');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('images');
    }
}
