<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Videos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('filename')->nullable();
            $table->string('owner');
            $table->string('videoId')->nullable();
            $table->string('kind')->nullable();
            $table->string('link')->nullable();
            $table->datetime('publishedAt')->nullable();
            $table->string('channelId')->nullable();
            $table->string('description')->nullable();
            $table->string('channelTitle')->nullable();
            $table->string('image')->nullable();
            $table->string('playlistId')->nullable();
            $table->string('title');
            $table->string('uri')->nullable();
            $table->string('created_date')->nullable();
            $table->string('size')->nullable();
            $table->string('source')->nullable();
            $table->text('tags')->nullable();
            $table->string('ownername')->nullable();
            $table->string('contributorlocation')->nullable();
            $table->integer('version')->nullable();
            $table->integer('dataset_id')->unsigned();
            $table->integer('class_id')->nullable();

            $table->rememberToken();
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
        Schema::drop('videos');
    }
}
