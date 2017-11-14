<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    public $table = "files";

    protected $fillable = ['filename','filepath','isFolder','parentFileId'];
}
