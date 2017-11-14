<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Tweets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tweets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('twitter_created_at');
            $table->integer('favorite_count');
            $table->boolean('favorited');
            $table->string('geo')->nullable();
            $table->text('text');
            $table->string('id_str');
            $table->string('twitter_id');
            $table->string('in_reply_to_screen_name')->nullable();
            $table->string('in_reply_to_status_id')->nullable();
            $table->string('in_reply_to_status_id_str')->nullable();
            $table->string('in_reply_to_user_id')->nullable();
            $table->string('in_reply_to_user_id_str')->nullable();
            $table->boolean('is_quote_status');
            $table->string('lang');
            $table->string('place')->nullable();
            $table->boolean('possibly_sensitive')->nullable();
            $table->boolean('retweeted');
            $table->integer('retweet_count');
            $table->string('source');
            $table->string('contributors')->nullable();
            $table->string('coordinates')->nullable();
            $table->text('user');
            $table->text('entities');
            $table->text('place_name');
            $table->string('country');
            $table->string('country_code');
            $table->text('place_full_name');
            $table->string('place_coordinates');
            $table->text('tag');

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
        Schema::drop('tweets');
    }
}
