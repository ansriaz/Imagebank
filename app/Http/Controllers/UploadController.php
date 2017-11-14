<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Input;
use Validator;
use Redirect;
use Response;
use Session;
use Auth;
use App\Logic\Services\CoreServices;
use App\Logic\Services\DBService;
Use App\Jobs\UploadDataset;

use App\Logic\Image\ImageRepository;

use Log;

class UploadController extends Controller {

    protected $image;

    public function __construct(ImageRepository $imageRepository, CoreServices $core, DBService $dbService)
    {
        $this->dbService = $dbService;
        $this->middleware('auth');
        $this->image = $imageRepository;
        $this->core = $core;
    }

    public function getUpload()
    {
        $data = array(
            'datasets' => $this->dbService->getAllDatasets()
        );
        return view('pages.upload.image')->with('responseData',$data);
    }

    public function postUpload()
    {
        $photo = Input::all();
        Log::info($photo);
        $response = $this->image->upload($photo);

        return $response;

    }

    public function deleteUpload()
    {

        $filename = Input::get('id');

        if(!$filename)
        {
            return 0;
        }

        Log::info($filename);
        $response = $this->image->delete( $filename );
        Log::info($response);

        return $response;
    }

    public function download()
    {
        $entry = Fileentry::where('filename', '=', $filename)->firstOrFail();
        $file = Storage::disk('local')->get($entry->filename);

        return (new Response($file, 200))
              ->header('Content-Type', $entry->mime);
    }

    public function getUploadDataset()
    {
        return view('pages.upload.dataset');
    }

    public function uploadDataset ()
    {
        $dataset_zip = Input::file('dataset_zip');
        Log::info('[DATASET_ZIP_FILE]: '.$dataset_zip);
        $dataset_zip_name = $dataset_zip->getClientOriginalName();
        $dataset_zip_size = round($dataset_zip->getSize() / 1024);
        $dataset_zip_ex = $dataset_zip->getClientOriginalExtension();
        $dataset_zip_mime = $dataset_zip->getMimeType();

        $target_dir = 'datasets';
        $target_dir = $this->core->makeFolder(public_path().'/'.$target_dir);

        if($dataset_zip->move($target_dir, $dataset_zip_name))
        {
            $job = new UploadDataset(Auth::id(), $dataset_zip_name, $target_dir);
            $this->dispatch( $job );

            return Redirect::to('/dashboard');

        } else {
            Log::info("There was a problem with the upload. Please try again.");
            Session::flash('flash_message', 'There was a problem with the upload. Please try again.');
        }
        // return Response::json([
        //     'error' => true,
        //     'message' => 'Kindly upload zip archive only.',
        //     'code' => 400
        // ], 400);
        return redirect()->back();
    }
}
