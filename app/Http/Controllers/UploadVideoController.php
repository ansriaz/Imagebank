<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;

use App\Logic\Services\CoreServices;
use App\Logic\Services\DBService;
use App\Logic\Video\VideoRepo;
use Validator;
use Redirect;
use Response;
use Session;
use Auth;

use Log;

class UploadVideoController extends BaseController
{
    protected $video;

    public function __construct(VideoRepo $video, CoreServices $core, DBService $dbService)
    {
        $this->dbService = $dbService;
        $this->middleware('auth');
        $this->core = $core;
        $this->video = $video;
    }

    public function getUploadVideo()
    {
        $data = array(
            'datasets' => $this->dbService->getAllDatasets()
        );
        return view('pages.upload.video')->with('responseData',$data);
    }

    public function postUploadVideo()
    {
        $photo = Input::all();
        Log::info($photo);
        $response = $this->video->upload($photo);

        Log::info('[RESPONSE] '.json_encode($response));

        if($response['result'] == '1')
        {
            Session::flash('success_message', 'Your video has been successfully uploaded / added.');
            return redirect('/dashboard');
        } else
        {
            $data = array(
                'datasets' => $this->dbService->getAllDatasets()
            );
            return view('pages.upload.video')->with('responseData',$data);
        }
    }

    public function deleteUploadVideo()
    {

        $filename = Input::get('id');

        if(!$filename)
        {
            return 0;
        }

        Log::info($filename);
        $response = $this->video->delete( $filename );
        Log::info($response);

        return $response;
    }
}
