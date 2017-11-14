<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebImage extends Model
{
    public $table = "web_image";

    // protected $fillable = ['title','filename'];
    protected $fillable = ['title','filename','link','uri','tags','source','created','author','contributorlocation','description','version','user_id','class_id','dataset_id'];
}
