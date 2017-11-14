<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFile extends Model
{
    public $table = "userfiles";

    protected $fillable = ['userId','fileId'];
}
