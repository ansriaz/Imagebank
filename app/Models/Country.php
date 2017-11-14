<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $table = "country";

    protected $fillable = ['ISO','ISO3','ISO-Numeric','fips','Country','Capital','Area','Population','Continent','tld','CurrencyCode','CurrencyName','Phone','PostalCodeFormat','PostalCodeRegex','Languages','geonameid','neighbours','EquivalentFipsCode'];
}

