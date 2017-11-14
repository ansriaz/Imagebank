<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\User;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use DB;
use Validator;
use Redirect;
use Session;
use Response;

use App\Models\Image;
use App\Models\Video;
use App\Models\Classlabel;
use App\Models\UserDatasets;
use App\Models\VideoClasses;
use App\Models\ImageClasslabels;
use App\Logic\Services\ZipService;
use App\Logic\Services\DBService;

use Log;

class UserController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
	public function __construct(ZipService $zip, DBService $dbService)
	{
		$this->middleware('auth');
		$this->zip = $zip;
		$this->dbService = $dbService;
	}

	public function index()
    {
        return view('user.dashboard');
    }

	public function updateInfo()
	{
		$input = Input::only('name','email','organization');

		$name = $input['name'];
		$email = $input['email'];
		$organization = $input['organization'];
		Log::info('[UPDATE INFO]: ' . json_encode($input));
		// Log::info('[EMAIL]: ' . $email);

		// $result = User::where('email','=', $email)->update(array('name' => $name, 'email' => $email, 'organization' => $organization));
		$updateUser = User::where('email','=',$email)->first();
		$updateUser->name = $name;
		$updateUser->organization = $organization;
		$updateUser->save();
		Log::info('[UPDATE INFO]: ' . json_encode($updateUser));

		Session::flash('flash_message', 'Profile updated successfully!');

		return redirect()->back();
	}

	public function updatePassword()
	{
		$input = Input::only('old_password','new_password');

		$old = $input['old_password'];

		$credentials = [
		'email' => Auth::user()->email,
		'password' => $old,
		];

		if(Auth::validate($credentials)) {
			User::where('email','=', Auth::user()->email)->update(array('password' => bcrypt($input['new_password'])));
			Session::flash('flash_message', 'Password changed successfully!');
		} else {
			Session::flash('flash_message', 'Incorrect old password!');
		}

		return redirect()->back();
	}

	public function getDatasets()
	{
		$type = Input::get('type');
		if (Auth::check())
		{
			$user_id = Auth::id();
			$userdatasets = UserDatasets::where('user_id', 'like', $user_id)
					  				  -> distinct()
					  				  -> get();

			// Log::info($userdatasets);

			$responseData = array(
                            'datasets' 	=> $userdatasets,
                            'page' 		=> 'user'
                        );

			Log::info('[GETDATASETS] ' . $type);
			if(isset($type) && !is_null($type))
			{
				if($type == 'videos')
					return view('fragment.videos')->with(['responseData' => $userdatasets,'page' => 'user']);
				else
					return view('fragment.images')->with(['responseData' => $userdatasets,'page' => 'user']);
			}

		} else {
			return redirect()->back()->with('flash_message', 'Invalid user credentials provided');
		}

	}

	public function getClasslabelsOfDataset($type)
	{
		$datasetTitle = Input::only('dataset_title');
		Log::info($datasetTitle);

		Log::info('[GetClasslabelsOfDataset] '.$type);
		if (Auth::check() && !is_null($datasetTitle))
		{

			$selectedDataset = $this->dbService->getUserDatasetByName($datasetTitle);
			$classlabels = Classlabel::where('dataset_id', '=', $selectedDataset->id)->paginate(5);

			$responseData = array(
                            'classlabels' => $classlabels,
                            'title' => $selectedDataset->title,
                            'type' => $type
                        );

			// Log::info('[GetClasslabelsOfDataset] '. json_encode($responseData));

            return view('user.albumsbyclass')->with('responseData',$responseData);
		} else {
			return redirect()->back()->with('flash_message', 'Invalid user credentials provided');
		}
	}

	public function getVideos ()
	{
		$d = Input::only('classlabel','dataset_title');
		Log::info('[GetDatasetByClasslabels]'.json_encode($d));

		if (Auth::check())
		{
			if(!is_null($d['classlabel']))
			{

				$selectedDataset = $this->dbService->getUserDatasetByName($d['dataset_title']);

				$videos = Video::join('video_classes', 'videos.id','=','video_classes.video_id')
						  -> join('classlabel', 'video_classes.class_id','=','classlabel.id')
						  -> select('videos.name', 'videos.filename', 'videos.tags', 'videos.link', 'videos.uri')
						  -> where('videos.dataset_id', $selectedDataset->id)
						  // -> whereNotNull('videos.name')
						  -> where('video_classes.class_id',$d['classlabel'])
						  -> paginate(5);

				$responseData = array(
	                            'classlabel' => $d['classlabel'],
	                            'dataset_title' => $d['dataset_title'],
	                            'videos' => $videos
	                        );

				// Log::info($responseData);

	            return view('fragment.playlist')->with('responseData',$responseData);

        	} else {

        		$d = Input::only('id');
				Log::info('[ID]'.json_encode($d));

				$videos = Video::where('dataset_id', $d['id'])
					  -> where('owner',Auth::id())
					  -> paginate(4);

				$responseData = array(
                    'id' => $d['id'],
                    'videos' => $videos
                );

				// Log::info($responseData);

	            return view('fragment.playlist')->with('responseData',$responseData);
        	}

		} else {
			return redirect()->back()->with('flash_message', 'Invalid user credentials provided');
		}
	}

	public function getDatasetByClasslabels()
	{
		$d = Input::only('classlabel','dataset_title');
		Log::info('[GetDatasetByClasslabels]'.json_encode($d));

		if (Auth::check())
		{
			if(!is_null($d['classlabel']))
			{

				$selectedDataset = $this->dbService->getUserDatasetByName($d['dataset_title']);

				$images = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
						  -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
						  -> select('images.name', 'images.filename', 'images.tags', 'images.link', 'images.uri')
						  -> where('images.dataset_id', $selectedDataset->id)
						  -> where('imageclasslabels.class_id',$d['classlabel'])
						  -> paginate(20);

				$responseData = array(
	                            'classlabel' => $d['classlabel'],
	                            'dataset_title' => $d['dataset_title'],
	                            'images' => $images
	                        );

				// Log::info($responseData);

	            return view('fragment.album')->with('responseData',$responseData);

        	} else {

        		$d = Input::only('id');
				Log::info('[ID]'.json_encode($d));

				$images = Image::where('dataset_id', $d['id'])
					  -> where('user_id',Auth::id())
					  -> paginate(12);

				$responseData = array(
                    'id' => $d['id'],
                    'images' => $images
                );

				// Log::info($responseData);

	            return view('fragment.album')->with('responseData',$responseData);
        	}

		} else {
			return redirect()->back()->with('flash_message', 'Invalid user credentials provided');
		}

	}

	public function getDatasetsByAscDate()
	{
		if (Auth::check())
		{
			$user_id = Auth::id();
			$images = DB::table('images')->where('user_id', $user_id)->orderBy('created_at','asc')->get();

			$date = null;
			$img = null;
			foreach ($images as $image) {

				$time = strtotime($image->created_at);
				$date = date('Y-m-d',$time);

				$img = $image;
				break;

			}

			// $data = array(
			// 	'uri' => $image->uri,
			// 	'name' => $image->name,
			// 	'filename' => $image->filename
			// 	);

            // $date = $images[0]->created_at;
			$datasets = array();
			$dataset = array();
			foreach ($images as $image) {
				$time = strtotime($image->created_at);
				$newformat = date('Y-m-d',$time);

				if($date === $newformat)
				{
					array_push($dataset, (array)$image);
				} 
				else 
				{
					$data = array(
						'dataset' => $dataset,
						'date' => $date
					);
					array_push($datasets, $data);
					$date = $newformat;
				}
			}

			$data = array(
				'dataset' => $dataset,
				'date' => $date
			);
			array_push($datasets, $data);
			// array_push($datasets, $dataset);

            // $obj = json_encode((array)$img);

			// return view('user.images')->with('dataset',$data);

			//foreach ($dataset as $image) {
			//     print_r($image);
			// }
            // print_r($dataset);
			// $d['$dataset'] = $dataset;
			// Log::info($datasets);
			return view('user.images')->with('datasets',$datasets);
			// return response()->json([$datasets]);
		} else {
			return redirect()->back()->with('flash_message', 'Invalid user credentials provided');
		}

        // $obj = json_encode((array)$dataset);
        // return $img->uri.$img->name;
        // return $obj;
        // return View::make('user.images').with('dataset',$obj);
        // redirect()->back()->json('datasets' => $datasets]);
	}

	public function download()
	{

		$checked = Input::get('dataset');
		$date = null;
		$images = [];
		if(is_array($checked))
		{
			// echo $checked['dataset'];
			foreach ($checked as $value)
			{
				$date = $value;
				$imagesFromTable = DB::table('images')
							->where('created_at', 'LIKE', '%'.$date.'%')
							->where('user_id', Auth::id())
							->get();
				foreach ($imagesFromTable as $img) {
					$images[] = $img;
				}
			}
		}

		$time = strtotime($date);
		$date = date('Y-m-d',$time);

		$file_names = array();
		foreach ($images as $image) {
			$file_names[] = $image->filename; //$image->uri.
		}

		//Archive name
		$archive_file_name = $date.'_'.Auth::id().'.zip';
		// public_path().'/dataset_archives/'.
		//Download Files path
		$file_path = public_path(). '/images/full_size';
		//cal the function

		$zipfile = $this->zip->createZipArchive($file_names,$archive_file_name,$file_path);

		$headers = array(
			'Content-Description: File Transfer',
			'Content-type: application/zip',
			'Content-Type: application/force-download', // some browsers need this
			// 'Content-Disposition: attachment; filename='$archive_file_name,
			'Expires: 0',
			'Cache-Control: must-revalidate, post-check=0, pre-check=0',
			'Pragma: public',
			// 'Content-Length': $zipped_size,
		);
		// ob_clean();
		// flush();
		// readfile("$archive_file_name");
		// unlink("$archive_file_name"); // Now delete the temp file (some servers need this option)

        return Response::download($archive_file_name, $archive_file_name, $headers);
	}
}
