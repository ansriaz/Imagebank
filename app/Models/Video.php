<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public static $rules = [
        'file' => 'required|mimes:mp4,mkv,avi,mov,qt,wmv'
    ];

    public static $messages = [
        'file.mimes' => 'Uploaded file is not in video format',
        'file.required' => 'Video is required'
    ];

    public $table = "videos";

    protected $fillable = ['name','filename','owner','videoId','kind','link','publishedAt','channelId','channelTitle','description','image','playlistId','title','datasetId','uri','created_date','size','source','tags','ownername','contributorlocation','version','dataset_id','class_id'];
}
