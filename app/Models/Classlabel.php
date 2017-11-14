<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classlabel extends Model
{

	public $table = "classlabel";

    protected $fillable = ['title','description'];

}
