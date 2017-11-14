<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDatasets extends Model
{

    public $table = "user_datasets";

    protected $fillable = ['id','title','description','user_id'];

}
