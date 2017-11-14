<?php

namespace App\Logic\Services\FlickrService;

use Auth;
use App\Models\ImageUrls;
use App\Models\Image;
use App\Models\UserDatasets;
use App\Models\Classlabel;
use App\Models\ImageClasslabels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

use App\Logic\Services\DBService;

use Log;

class FlickrToDB
{

    public function __construct(DBService $dbService)
    {
        $this->dbService = $dbService;
    }

    public function saveInDB( $photo, $tag, $classlbls, $newVersion )
    {
        // Log::info($photo);

        Log::info('[NEW VERSION SAVE_TO_DB] '. $newVersion);

        $tags = $photo['tags'];
        // Log::info($tags);

        $classlabels = $classlbls;

        $dataset_name = 'flickr';
        if (!file_exists(public_path().'/images/'.$dataset_name)) {
            mkdir($dir, 0777, true);
        }
        $dataset_id = $this->addDatasetOfImages($dataset_name,'images added by flickr crawler');
        // Log::info($dataset_id);
        // $classlabels = preg_replace('/\s+/', '', $classlabels);
        // $labels = null;
        // if ($classlabels != '') {
        //     $labels = explode(',', $classlabels);
        $classlbl = $this->updateClassLabels( $classlabels , $dataset_id );
        //     Log::info($labels);
        // }

        $dir_path = '/images/'.$dataset_name.'/';

        $dir = public_path().$dir_path.$classlabels;
        // Log::info($dir);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $dir = $dir_path.$classlabels.'/';

        $originalName = $photo['id'].'_'.$photo['secret'];

        $alreadyAdded = Image::where('name','like',$originalName)->first();
        if (!is_null($alreadyAdded)) {
            $this->addImageUrls($alreadyAdded, $photo);
            $this->updateDateTaken($alreadyAdded, $photo);
            Log::info($originalName . ' is already added');

            $this->updateTags($alreadyAdded, $photo, $tag);
            
            return ;
        }

        $filename = $this->sanitize($photo['id']);

        $allowed_filename = $this->createUniqueFilename( $filename, $dir );

        $filenameExt = $allowed_filename .'.jpg';

        $photo_url = null;
        if(!empty($photo['url_z']))
            $photo_url = $photo['url_z'];
        else if(!empty($photo['url_c']))
            $photo_url = $photo['url_c'];
        else if(!empty($photo['url_b']))
            $photo_url = $photo['url_b'];
        else if(!empty($photo['url_n']))
            $photo_url = $photo['url_n'];
        else if(!empty($photo['url_o']))
            $photo_url = $photo['url_o'];
        else if(!empty($photo['url_h']))
            $photo_url = $photo['url_h'];
        else if(!empty($photo['url_s']))
            $photo_url = $photo['url_s'];
        else
            $photo_url = $photo['url_m'];

        try {
            $uploadSuccess1 = $this->original( $photo, $filenameExt, $photo_url, $dir );
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            return;
        }   

        // if( !$uploadSuccess1 )
        // {
        //     $uploadSuccess1 = $this->original( $photo, $filenameExt, $photo_url, $dir );
        // }

        $sessionImage = new Image;
        $sessionImage->name = $originalName;
        $sessionImage->filename      = $filenameExt;
        $sessionImage->link = $photo_url;
        $sessionImage->uri = $dir;
        $sessionImage->tags = $tags;
        $sessionImage->source = 'flickr';
        $sessionImage->date_taken = $photo['datetaken'];
        // $sessionImage->author = $photo['owner'].'_'.$photo['ownername'];
        $sessionImage->contributorlocation = $photo['latitude'].' '.$photo['longitude'];
        $sessionImage->description = $photo['description']['_content'];
        $sessionImage->version = $newVersion;
        $sessionImage->user_id = 1;
        $sessionImage->dataset_id = $dataset_id;

        $sessionImage->photo_id = $photo['id'];
        $sessionImage->owner = $photo['owner'];
        $sessionImage->secret = $photo['secret'];
        $sessionImage->server = $photo['server'];
        $sessionImage->farm = $photo['farm'];
        $sessionImage->title = $photo['title'];
        $sessionImage->is_public = $photo['ispublic'];
        $sessionImage->license = $photo['license'];
        $sessionImage->date_uploaded = $photo['dateupload'];
        $sessionImage->date_last_update = $photo['lastupdate'];
        $sessionImage->ownername = $photo['ownername'];
        $sessionImage->accuracy = $photo['accuracy'];
        $sessionImage->views = $photo['views'];
        $sessionImage->pathalias = $photo['pathalias'];
        $sessionImage->machine_tags = $photo['machine_tags'];
        if(isset($photo['place_id'])){
            $sessionImage->place_id = $photo['place_id'];
        }
        if(isset($photo['woeid'])){
            $sessionImage->woeid = $photo['woeid'];
        }
        $sessionImage->media = $photo['media'];
        $sessionImage->media_status = $photo['media_status'];
        if(isset($photo['geo_is_public'])){
            $sessionImage->geo_is_public = $photo['geo_is_public'];
        }

        $sessionImage->class_id = $classlbl->id;
        $sessionImage->save();

        // ========= ADD urls of image ==========
        $this->addImageUrls($sessionImage, $photo);

        if($classlabels != '')
        {
            $this->addClassLabelsOfImages($classlabels, $sessionImage->id, $dataset_id);
        }

        return Response::json([
            'error' => false,
            'code'  => 200
        ], 200);

    }

    public function createUniqueFilename( $filename, $dir_path )
    {
        $full_size_dir = public_path() . $dir_path;
        $full_image_path = $full_size_dir . $filename . '.jpg';

        if ( File::exists( $full_image_path ) )
        {
            // Generate token for image
            $imageToken = substr(sha1(mt_rand()), 0, 5);
            return $filename . '-' . $imageToken;
        }

        return $filename;
    }

    /**
     * Optimize Original Image
     */
    public function original( $photo, $filename, $url, $dir_path )
    {

        ini_set('memory_limit', '512M');
        Log::info($url);
        // Log::info($dir_path);
        try {
           $data = file_get_contents($url);
           if($data === FALSE){
                return;
           }
           $destinationPath = public_path() . $dir_path;
           Log::info($destinationPath);

            if (!is_dir($destinationPath)) {
                mkdir($destinationPath);
            }

            // Log::info($destinationPath);
            $image = file_put_contents($destinationPath.$filename, $data); 

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            return;
        }

        return $image;
    }

    function sanitize($string, $force_lowercase = true, $anal = false)
    {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;

        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }

    function updateClassLabels($label, $dataset_id)
    {
        if ($label != '') {
            $lbl = Classlabel::where('title', '=', $label)->where('dataset_id','like',$dataset_id)->first();
            // $lbl = Classlabel::where('title', '=', $label)->first();
            if (is_null($lbl)) {
                $classlabel = new Classlabel;
                $classlabel->title = $label;
                $classlabel->dataset_id = $dataset_id;
                $classlabel->save();

                return $classlabel;
            } else {
                return $lbl;
            }
        }
    }

    function addClassLabelsOfImages($label, $image_id,$dataset_id)
    {
        // foreach ($classlabels as $label) {
            $lbl = Classlabel::where('title', '=', $label)->where('dataset_id','like',$dataset_id)->first();
            // $label = Classlabel::where('title', '=', $label)->first();

            $imageLabel = new ImageClasslabels;
            $imageLabel->class_id = $lbl->id;
            $imageLabel->image_id = $image_id;
            $imageLabel->save();
        // }
    }

    function addDatasetOfImages($name, $des)
    {
        $alreadyAdded = UserDatasets::where('title', '=', $name)->first();
        if (is_null($alreadyAdded)) {
            $dataset = new UserDatasets;
            $dataset->user_id = 1;
            $dataset->title = $name;
            $dataset->description = $des;
            $dataset->save();

            return $dataset->id;
        }

        return $alreadyAdded->id;
    }

    function addImageUrls ($imageObj, $photo) 
    {
        $old = ImageUrls::where('imageid','=',$imageObj->id)->first();
        if(is_null($old) || !isset($old))
        {
            $newObj = new ImageUrls;
            $newObj->name = $imageObj->name;
            $newObj->imageid = $imageObj->id;
            $newObj->filename = $imageObj->filename;
            if(!empty($photo['url_o']))
                $newObj->original = $photo['url_o'];
            if(!empty($photo['url_t']))
                $newObj->thumbnail = $photo['url_t'];
            if(!empty($photo['url_sq']))
                $newObj->ssmall = $photo['url_sq'];
            if(!empty($photo['url_q']))
                $newObj->qlarge = $photo['url_q'];
            if(!empty($photo['url_s']))
                $newObj->msmall = $photo['url_s'];
            if(!empty($photo['url_n']))
               $newObj->nsmall = $photo['url_n'];
            if(!empty($photo['url_m']))
                $newObj->medium = $photo['url_m'];
            if(!empty($photo['url_z']))
                $newObj->zmediun = $photo['url_z'];
            if(!empty($photo['url_c']))
                $newObj->cmedium = $photo['url_c'];
            if(!empty($photo['url_b']))
                $newObj->blarge = $photo['url_b'];
            if(!empty($photo['url_h']))
                $newObj->hlarge = $photo['url_h'];
            if(!empty($photo['url_k']))
                $newObj->klarge = $photo['url_k'];
            $newObj->save();
        }
    }

    function updateDateTaken ($imageObj, $photo) 
    {
        if(!empty($photo['datetaken']))
        {
            $imageObj->date_taken = $photo['datetaken'];
            $imageObj->save();   
        }
    }

    function updateTags ($imageObj, $photo, $tag)
    {
        $mystring = $imageObj->tags;
        $pos = strpos($mystring, $tag);
        // Log::info($mystring .' # '. $tag);
        if($pos === false){
            Image::where('id',$imageObj->id) -> update(['tags'=>$imageObj->tags .','.$tag]);
        }
        // $this->addClassLabelsOfImages($classlabels, $alreadyAdded->id);
    }

}
