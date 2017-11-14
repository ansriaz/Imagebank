<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoClasses extends Model
{
    public $table = 'video_classes';

    protected $fillable = ['video_id','class_id'];
}
