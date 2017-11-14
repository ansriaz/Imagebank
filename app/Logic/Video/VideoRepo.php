<?php

namespace App\Logic\Video;

use Auth;
use App\Models\Video;
use App\Models\Classlabel;
use App\Models\UserDatasets;
use App\Models\VideoClasses;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use App\Logic\Services\DBService;
use App\Logic\Services\CoreServices;

use Session;
use Log;

class VideoRepo
{

    public function __construct(DBService $dbService, CoreServices $core)
    {
        $this->dbService = $dbService;
        $this->core = $core;
    }

    public function upload( $form_data )
    {

        $videolink = null;
        $videotitle = null;
        if(!isset($form_data['video']))
        {
            // get video link
            $videolink = $form_data['videolink'];
            Log::info("[INPUT_VIDEO_LINK] ".$videolink);

            if(is_null($videolink))
            {
                // redirect
                Session::flash('error_video', 'Kindly add the video (size 20MB) or add the link property for the video.');
                return Redirect::to('/video/upload');
                // return Response::json([
                //     'error' => true,
                //     'message' => "Kindly select dataset from the list or write new name for dataset.",
                //     'code' => 400
                // ], 400);
            }

            // get video title
            $videotitle = $form_data['videotitle'];
            Log::info("[INPUT_VIDEO_TITLE] ".$videotitle);
        } else {
            // get file
            $video = $form_data['video'];
            // Log::info($video);
        }

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
                Session::flash('error_video', 'Kindly select dataset from the list or write new name for dataset in the given box.');
                return Redirect::to('/video/upload');
                // return Response::json([
                //     'error' => true,
                //     'message' => "Kindly select dataset from the list or write new name for dataset.",
                //     'code' => 400
                // ], 400);
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
        if( count($classes) == 0 )
        {
            Session::flash('error_video', 'Kindly select class from the list or write new name for dataset in the given box.');
            return Redirect::to('/video/upload');
            // return Response::json([
            //     'error' => true,
            //     'message' => "Kindly select class from the list or write in the given box.",
            //     'code' => 400
            // ], 400);
        }

        $this->updateClassLabels( $classes, $ds_available->id );
        Log::info($classes);

        $dir_name = '/videos';
        $dir  = $this->core->makeFolder(public_path().DIRECTORY_SEPARATOR.$dir_name);
        $dir_path = $dir_name.DIRECTORY_SEPARATOR.$ds_available->title.'/';

        if(isset($video))
        {
            $originalName = $video->getClientOriginalName();
            $originalNameWithoutExt = substr($originalName, 0, strlen($originalName) - 4);
            $originalExt = substr($originalName, strlen($originalName) - 4, strlen($originalName));

            $filename = $this->sanitize($originalNameWithoutExt);

            // $allowed_filename = $this->createUniqueFilename( $filename, $dir_path );

            $filenameExt = $filename . $originalExt;

            $uploadSuccess1 = $this->original( $video, $filenameExt, $dir_path );

            if( !$uploadSuccess1 )
            {
                Session::flash('error_video', 'Server error while uploading.');
                return Redirect::to('/video/upload');
                // return Response::json([
                //     'error' => true,
                //     'message' => 'Server error while uploading',
                //     'code' => 500
                // ], 500);
            }
        }
        if(isset($videotitle))
        {
            $originalExt = substr($videotitle, strlen($videotitle) - 4, strlen($videotitle));
        }

        $mytime = date('Y/m/d H:i:s');

        $user_id = null;

        if (Auth::check())
        {
            $user_id = Auth::id();
        }

        $sessionVideo = new Video;
        if(isset($video))
        {
            $sessionVideo->filename = $filenameExt;
            $sessionVideo->name = $originalName;
            $sessionVideo->uri = $dir_path;
            $sessionVideo->size = filesize($uploadSuccess1);
        }
        if(isset($videolink))
        {
            $sessionVideo->name = $videotitle;
            $sessionVideo->link = $videolink;
        }
        $sessionVideo->kind = $originalExt;
        $sessionVideo->tags = $tags;
        $sessionVideo->created_date = $mytime;
        $sessionVideo->source = 'user';
        $sessionVideo->ownername = preg_replace('/\s+/', '', strtolower(Auth::user()->name));
        $sessionVideo->version = 1;
        $sessionVideo->owner = $user_id;
        $sessionVideo->dataset_id = $ds_available->id;
        $sessionVideo->save();

        if($classes != '')
        {
            $this->addClassLabels($classes, $sessionVideo->id, $ds_available->id);
        }

        return array('result'=>'1');

    }

    public function createUniqueFilename( $filename, $dir_path )
    {
        $full_size_dir = public_path() . $dir_path;
        // $full_image_path = $full_size_dir . $filename . '.jpg';

        if ( File::exists( $full_image_path ) )
        {
            // Generate token for video
            $imageToken = substr(sha1(mt_rand()), 0, 5);
            return $filename . '-' . $imageToken;
        }

        return $filename;
    }

    /**
     * Optimize Original Image
     */
    public function original( $video, $filename, $dir_path )
    {

        $destinationPath = public_path() . $dir_path;

        $video = $video->move($destinationPath, $filename);

        return $video;
    }

    /**
     * Delete Image From Session folder, based on original filename
     */
    public function delete( $originalFilename, $dir_path )
    {

        $full_size_dir = public_path() . $dir_path;
        // $icon_size_dir = public_path() . '/images/icon_size/';

        $sessionVideo = Image::where('name', 'like', $originalFilename)->first();


        if(empty($sessionVideo))
        {
            return Response::json([
                'error' => true,
                'code'  => 400
            ], 400);

        }

        $full_path1 = $full_size_dir . $sessionVideo->filename . '.jpg';
        $full_path2 = $icon_size_dir . $sessionVideo->filename . '.jpg';

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

        if( !empty($sessionVideo))
        {
            $sessionVideo->delete();
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

    function addClassLabels($classlabels, $v_id, $dataset_id)
    {
        foreach ($classlabels as $label) {
            $lbl = Classlabel::where('title', '=', $label)->where('dataset_id','like',$dataset_id)->first();
            // $label = Classlabel::where('title', '=', $label)->first();

            $v_label = new VideoClasses;
            $v_label->class_id = $lbl->id;
            $v_label->video_id = $v_id;
            $v_label->save();
        }
    }
}
