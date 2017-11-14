<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageClasslabels extends Model
{

	public $table = 'imageclasslabels';

    protected $fillable = ['image_id','class_id'];

}
