<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleImage extends Model
{
    public $table = "google_image";

    protected $fillable = ['name','country','images_count'];
}
