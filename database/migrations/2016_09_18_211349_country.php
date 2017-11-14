<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Country extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country', function (Blueprint $table) {
            $table->increments('id');
            $table->string('iso');
            $table->string('iso3');
            $table->string('iso_numeric');
            $table->string('fips');
            $table->string('country');
            $table->string('capital');
            $table->string('area');
            $table->string('population');
            $table->string('continent');
            $table->string('tld');
            $table->string('currency_code');
            $table->string('currency_name');
            $table->string('phone');
            $table->string('postalcode_format');
            $table->string('postalcode_regex');
            $table->string('languages');
            $table->string('geonameid');
            $table->string('neighbours');
            $table->string('equivalent_fips_code');

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
        Schema::drop('country');
    }
}
