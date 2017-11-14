<?php

namespace App\Logic\Image;

use Auth;
use App\Models\Image;
use App\Models\Classlabel;
use App\Models\UserDatasets;
use App\Models\ImageClasslabels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use App\Logic\Services\DBService;

use Session;
use Log;

class ImageRepository
{

    public function __construct(DBService $dbService)
    {
        $this->dbService = $dbService;
    }

    public function upload( $form_data )
    {
        // Log::info($form_data);

        //validating images
        $validator = Validator::make($form_data, Image::$rules, Image::$messages);

        Log::info($validator->messages()->first());

        if ($validator->fails()) {

            return Response::json([
                'error' => true,
                'message' => $validator->messages()->first(),
                'code' => 400
            ], 400);

        }

        // get file
        $photo = $form_data['file'];
        // Log::info($photo);

        // get tags
        $tags = $form_data['tags'];
        Log::info("[INPUT_TAGS] ".$tags);

        // get dataset
        $selected_dataset = preg_replace('/\s+/', '', $form_data['dataset']);
        Log::info("[INPUT_SELECTED_DATASET] ".$selected_dataset);

        // get dataset name
        $ds_name = preg_replace('/\s+/', '', $form_data['datasetname']);
        Log::info("[INPUT_DATASET_NAME] ".$ds_name);

        // get the dataset name
        $ds_available = null;
        if(!is_null($ds_name) && $ds_name != "")
        {
            // create dataset
            // $user_login_time = session()->get('starttime');
            // $dataset_name = Auth::id().'_'.$user_login_time;
            $ds_available = $this->dbService->addDataset($ds_name,'dataset added by '.Auth::user()->name);
            Log::info($ds_available);
        } else {

            if(is_null($selected_dataset))
            {
                return Response::json([
                    'error' => true,
                    'message' => "Kindly select dataset from the list or write new name for dataset.",
                    'code' => 400
                ], 400);
            }

            $ds_available = $this->dbService->getUserDatasetByName($selected_dataset);
            Log::info($ds_available);
        }

        // get sub dataset
        $subdataset = $form_data['subdataset'];
        Log::info("[INPUT_SUB_DATASET] ".$subdataset);

        // get classlabels
        $classes = array();
        if(!is_null($subdataset) && $subdataset != "Select Class")
        {
            array_push($classes, $subdataset);
            //$tihs->dbService->getClassIdByTitleAndDatasetId($subdataset,$ds_available->id);
        } else {
            $classlabels = $form_data['classlabels'];
            Log::info('[CLASSLABELS] '.$classlabels);
            $classlabels = preg_replace('/\s+/', '', $classlabels); //remove spaces
            if ($classlabels != '')
            {
                $classes = explode(',', $classlabels);
            }
        }
        if( count($classes)==0 )
        {
            return Response::json([
                'error' => true,
                'message' => "Kindly select class from the list or write in the given box.",
                'code' => 400
            ], 400);
        }

        $this->updateClassLabels( $classes, $ds_available->id );
        Log::info($classes);

        $dir_path = '/images/'.$ds_available->title.'/';

        $originalName = $photo->getClientOriginalName();
        $originalNameWithoutExt = substr($originalName, 0, strlen($originalName) - 4);

        $filename = $this->sanitize($originalNameWithoutExt);

        $allowed_filename = $this->createUniqueFilename( $filename, $dir_path );

        $filenameExt = $allowed_filename .'.jpg';

        $uploadSuccess1 = $this->original( $photo, $filenameExt, $dir_path );

        // $uploadSuccess2 = $this->icon( $photo, $filenameExt );

        // if( !$uploadSuccess1 || !$uploadSuccess2 ) {

        if( !$uploadSuccess1 ) {

            return Response::json([
                'error' => true,
                'message' => 'Server error while uploading',
                'code' => 500
            ], 500);

        }

        $mytime = date('Y/m/d H:i:s');

        $user_id = null;

        if (Auth::check())
        {
            $user_id = Auth::id();
        }

        $sessionImage = new Image;
        $sessionImage->filename = $filenameExt;
        $sessionImage->name = $originalName;
        $sessionImage->uri = '/images/'.$ds_available->title.'/';
        $sessionImage->tags = $tags;
        $sessionImage->date_taken = $mytime;
        $sessionImage->source = 'user';
        // $sessionImage->contributorloation = ;
        $sessionImage->user_id = $user_id;
        $sessionImage->version = 1;
        $sessionImage->owner = $user_id;
        $sessionImage->dataset_id = $ds_available->id;
        $sessionImage->save();

        if($classes != '')
        {
            $this->addClassLabelsOfImages($classes, $sessionImage->id, $ds_available->id);
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
    public function original( $photo, $filename, $dir_path )
    {

        $destinationPath = public_path() . $dir_path;
        // $manager = new ImageManager();
        // $image = $manager->make( $photo )->encode('jpg')->save($destinationPath . $filename );
        $image = $photo->move($destinationPath, $filename);
        // Log::info($image);

        return $image;
    }

    /**
     * Create Icon From Original
     */
    public function icon( $photo, $filename )
    {
        $destinationPath = public_path() . '/images/icon_size/';
        $manager = new ImageManager();
        $image = $manager->make( $photo )->encode('jpg')->resize(200, null, function($constraint){$constraint->aspectRatio();})->save( $destinationPath . $filename );

        // Log::info($image);

        return $image;
    }

    /**
     * Delete Image From Session folder, based on original filename
     */
    public function delete( $originalFilename, $dir_path )
    {

        $full_size_dir = public_path() . $dir_path;
        // $icon_size_dir = public_path() . '/images/icon_size/';

        $sessionImage = Image::where('name', 'like', $originalFilename)->first();


        if(empty($sessionImage))
        {
            return Response::json([
                'error' => true,
                'code'  => 400
            ], 400);

        }

        $full_path1 = $full_size_dir . $sessionImage->filename . '.jpg';
        $full_path2 = $icon_size_dir . $sessionImage->filename . '.jpg';

        Log::info($full_path1);
        Log::info($full_path2);

        if ( File::exists( $full_path1 ) )
        {
            File::delete( $full_path1 );
        }

        if ( File::exists( $full_path2 ) )
        {
            File::delete( $full_path2 );
        }

        if( !empty($sessionImage))
        {
            $sessionImage->delete();
        }

        return Response::json([
            'error' => false,
            'code'  => 200
        ], 200);
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

    function updateClassLabels($classlabels, $dataset_id)
    {
        foreach ($classlabels as $label) {
            if ($label != '') {
                $lbl = Classlabel::where('title', '=', $label)->where('dataset_id','like',$dataset_id)->first();
                // $lbl = Classlabel::where('title', '=', $label)->first();
                if (is_null($lbl)) {
                    $classlabel = new Classlabel;
                    $classlabel->dataset_id = $dataset_id;
                    $classlabel->title = $label;
                    $classlabel->save();
                }
            }
        }
    }

    function addClassLabelsOfImages($classlabels, $image_id, $dataset_id)
    {
        foreach ($classlabels as $label) {
            $lbl = Classlabel::where('title', '=', $label)->where('dataset_id','like',$dataset_id)->first();
            // $label = Classlabel::where('title', '=', $label)->first();

            $imageLabel = new ImageClasslabels;
            $imageLabel->class_id = $lbl->id;
            $imageLabel->image_id = $image_id;
            $imageLabel->save();
        }
    }
}
