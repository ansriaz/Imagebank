<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Logic\Services\DBService;
use App\Models\ImageClasslabels;
use App\Models\UserDatasets;
use App\Models\Classlabel;

class Image extends Model
{
    public static $rules = [
        'file' => 'required|mimes:png,jpeg,jpg'
    ];

    public static $messages = [
        'file.mimes' => 'Uploaded file is not in image format',
        'file.required' => 'Image is required'
    ];

    public $table = "images";

    protected $fillable = ['id','name','title','filename','link','uri','tags','source','author','contributorlocation','description','version','user_id','class_id','dataset_id','photo_id','owner','secret','server','farm','is_public','license','date_uploaded','date_last_update','date_taken','ownername','views','accuracy','pathalias','machine_tags','place_id','woeid','geo_is_public','media','media_status'];

    // public function ClassLabel()
    // {
    // 	// $clabels = Classlabel::where('dataset_id', '=', $datasetId)->get();
    // 	$imgClabel = ImageClasslabels::where('image_id','=',$image_id)->first();
    // 	return $imgClabel->class_id;
    // }
}
