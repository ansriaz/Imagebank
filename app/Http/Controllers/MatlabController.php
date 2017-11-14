<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Logic\Services\DBService;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use App\Logic\Services\CoreServices;
Use App\Jobs\MatlabJob;

use Session;
use Log;
use DB;
use Auth;
use App\User;
use App\Models\Image;

use App\Models\File;
use App\Models\UserFile;

class MatlabController extends Controller
{
	public function __construct(DBService $dbService, CoreServices $core)
    {
		$this->middleware('auth');
        $this->dbService = $dbService;
        $this->core = $core;
    }

    public function index()
    {
    	set_time_limit(0);
		$json = [];
		$file_array = '';
		if (Auth::check())
		{
			$user_id = Auth::id();
			$files = UserFile::where('userId','=',$user_id)->get();
			Log::info('[USERFILES]: '.$files);
			if(!is_null($files) && isset($files) && count($files) > 0)
			{
				$file_array = $files;
			} else {
				$this->setupProject($user_id);
				$file_array = UserFile::where('userId','=',$user_id)->get();
			}
        }
        foreach ($file_array as $obj) {
			$file = File::where('id','=',$obj->fileId)->first();
			array_push($json, $file);
        }
        // Log::info($json);
        // $responseData = array('files'=>$json);
        // $tree = array('text'=> "Parent 1",'nodes'=> array('text'=> "Child 1",'nodes'=> array('text'=> "Grandchild 1",'text'=> "Grandchild 2"),'text'=> "Child 2"),'text'=> "Parent 2",'text'=> "Parent 3",'text'=> "Parent 4",'text'=> "Parent 5");
        return view('pages.matlab')->with('files', $json);
        // ->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0')
    }

    public function addFile ()
    {
		$filename = Input::get('fname');
		$folder = Input::get('folder');
		$parent = Input::get('parent');
		$user_id = Auth::id();

		$files = UserFile::where('userId','=',$user_id)->get();
		// Log::info('[USERFILES]: '.$files);
		if(is_null($files) && !isset($files) && count($files) == 0)
		{
			$this->setupProject($user_id);
		}
		// $requiredFolderObj = null;
		// foreach ($files as $f) {
		// 	$dirPath = File::where('id','=',$f->fileId)->first();
		// 	if($dirPath->filename == $folder) {
		// 		$requiredFolderObj = $dirPath;
		// 	}
		// }
		// Log::info($requiredFolderObj);

		$parentFolder = File::join('userfiles','files.id','=','userfiles.fileId')
						-> where('files.id','=',$parent)
						-> where('userfiles.userId','=',$user_id)
						-> first();

		// Log::info('[ParentFolder]: '.$parentFolder);

		if(isset($filename))
		{
			Log::info('[FILENAME]=>'.$filename.' [PARENT]=>'.$parent);

			$this->createFile($parentFolder,$filename,$user_id); //.'.m'
		}
		if(isset($folder))
		{
			Log::info('[FOLDER]=>'.$folder.' [PARENT]=>'.$parent);

			$this->createFolder($parentFolder,$folder,$user_id);
		}

		// $json = [];
  		// foreach ($file_array as $obj) {
		// 	$file = File::where('id','=',$obj->fileId)->first();
		// 	array_push($json, $file);
  		// }
		$json = $this->refresh();
        // Log::info($json);
        return $json;
    }

    public function saveFile()
    {
		$fileid = Input::get('fileid');
		$content = Input::get('content');
		Log::info('[FileId]: '.$fileid.' [Content]: '.$content);

		$file = File::where('id','=',$fileid)->first();
		Log::info($file);
		$destinationPath = $file->filepath;
		$physicalPath = public_path().'/'.$destinationPath;
		$mat_file = fopen($physicalPath, "w");
		if ($content != '') {
			fwrite($mat_file, $content);
		} else {
			ftruncate($mat_file, 0);
		}
		fclose($mat_file);

		return array('response'=> 'true');
    }

    public function saveAll()
    {

    }

    public function runMatlab ()
    {
		// $searchTerm = Input::get('searchterm');

		$user_id = Auth::id();
		$file = File::join('userfiles','files.id','=','userfiles.fileId')
						-> where('userfiles.userId','=',$user_id)-> first();
						// -> where('files.filename','=','app_main.mat')
		Log::info($file);

		// Log::info($searchTerm);
		// $this->searchImages($searchTerm);

		// search images.csv file in the project folder
		$csvFile = public_path().$file->filepath.'/images.csv';
		if ( 0 == filesize( $csvFile ) )
		{
			Log::info('[FILESIZE]: '.filesize($csvFile));
			return ['status'=>'no'];
		}

		Log::info('[FILESIZE]: '.$csvFile);

		// \Artisan::call('matlab', array('user_id' => '1', 'project' => $file->filename, 'images' => $searchTerm));
		$job = new MatlabJob($user_id, $file->filename);
		$this->dispatch( $job );
    }

    public function refresh()
    {
		$user_id = Auth::id();
		$files = UserFile::where('userId','=',$user_id)->get();
		$json = [];
        foreach ($files as $obj) {
			$file = File::where('id','=',$obj->fileId)->first();
			array_push($json, $file);
        }
        return $json;
    }

    /**
    * Run first time and setups a project with required files
    * Check if user directory (user_$user_id) is already there or not. If not then it will create one for every user
	* Creates user's project directory (Project_$user_id). It will call every time when user will create a new project (not yet implemented)
	* Creates one app_main.m file as a starting point of a project
	* Creates one images.csv file to read the images path to use it later for processing
	*
	* @return
    */
    function setupProject ($user_id)
    {
		$user = User::where('id','=',$user_id)->first();
		$rootDirectory = $this->createProjectsDirectory();

		// $dir_path = $public_dir.'/user_'.$user_id;
		Log::info('[rootDirectory]: '.$rootDirectory);
		$path_to_user_dir = $rootDirectory.'/user_'.$user_id;
		$dir  = $this->core->makeFolder(public_path().'/'.$path_to_user_dir);
		// $dir = public_path().'/'.$path_to_user_dir;
		// if (!file_exists($dir))
		// {
		// 	mkdir($dir, 0777, true);
		// }

		//Project folder
		Log::info('[user folder]: '.$dir);
		$name = str_replace(' ', '', $user->name);
		$dir_name = 'Project_'.$name; //.'_'.date('j_n_y')

		Log::info('[project_ folder]: '.$dir_name);
		$path_to_project_dir = $path_to_user_dir.'/'.$dir_name;
		$dir  = $this->core->makeFolder(public_path().'/'.$path_to_project_dir);
		// $dir = public_path().'/'.$path_to_project_dir;
		// if (!file_exists($dir))
		// {
		// 	mkdir($dir, 0777, true);
		// }

		$d = '';
		Log::info('[folder]: '.$dir);
		if(is_dir($dir))
		{
			$d = new File;
			$d->filename = $dir_name;
			$d->filepath = $path_to_project_dir;
			$d->isFolder = 1;
			$d->parentFileId = -1;
			$d->save();

			Log::info('[SETUP]: '.$d->id);

			$add_dir = new UserFile;
			$add_dir->userId = $user_id;
			$add_dir->fileId = $d->id;
			$add_dir->save();

			$this->createFile($d,'app_main.m',$user_id);
			$this->createCSVFile($d,'images.csv',$user_id);
		}
    }

    function createFolder ( $dir, $foldername, $user_id )
    {
		$destinationPath = $dir->filepath.'/'.$foldername;
		$physicalPath = $this->core->makeFolder(public_path().'/'.$destinationPath);

		// $physicalPath = public_path().'/'.$destinationPath;
		// if (!file_exists($physicalPath))
		// {
		// 	mkdir($physicalPath, 0777, true);
		// }

		$d = new File;
		$d->filename = $foldername;
		$d->filepath = $destinationPath;
		$d->isFolder = 1;
		$d->parentFileId = $dir->id;
		$d->save();

		$add_dir = new UserFile;
		$add_dir->userId = $user_id;
		$add_dir->fileId = $d->id;
		$add_dir->save();
    }

    function createFile ( $dir, $filename, $user_id )
    {
		$destinationPath = $dir->filepath.'/'.$filename;
		$physicalPath = public_path().'/'.$destinationPath;
		$matFile = fopen($physicalPath, "w");
		$content = '% Copy these lines to read images.csv and get the link to images
% Kindly search images before to run your script.
%
% fid = fopen(\'images.csv\');
% out = textscan(fid,\'%s%s%s%s%s%s\',\'delimiter\',\',\');
% fclose(fid);
% out{:,1}
%
% Thanks
% ImageBank';
		fwrite($matFile, $content);
		fclose($matFile);

		$file = new File;
		$file->filename = $filename;
		$file->filepath = $destinationPath;
		$file->isFolder = 0;
		$file->parentFileId = $dir->id;
		$file->save();

		Log::info('[FILE_OBJ]=>'.$file);

		$user_file = new UserFile;
		$user_file->userId = $user_id;
		$user_file->fileId = $file->id;
		$user_file->save();
		Log::info('[USER_FILE_OBJ]=>'.$user_file);

		return $file;
    }

    function createCSVFile ( $dir, $filename, $user_id )
    {
		$destinationPath = $dir->filepath.'/'.$filename;
		$physicalPath = public_path().'/'.$destinationPath;
		$csvFile = fopen($physicalPath, "w");
		fclose($csvFile);

		$file = new File;
		$file->filename = $filename;
		$file->filepath = $destinationPath;
		$file->isFolder = 0;
		$file->parentFileId = $dir->id;
		$file->save();

		Log::info('[FILE_OBJ]=>'.$file);

		$user_file = new UserFile;
		$user_file->userId = $user_id;
		$user_file->fileId = $file->id;
		$user_file->save();
		Log::info('[USER_FILE_OBJ]=>'.$user_file);

		return $file;
    }

    public function searchImagesForMatlab ( )
    {
    	set_time_limit(0);

		$user_id = Auth::id();

		$searchTerm = Input::get('searchterm');

		$query_param = preg_replace('/\s+/', '', $searchTerm);

        $query_array = null;

        if ($query_param != '') {
            $query_array = explode(',', $query_param);
        }
        Log::info($query_array);

        DB::connection()->disableQueryLog();
        // DB::connection()->enableQueryLog();

        $project = File::join('userfiles','files.id','=','userfiles.fileId')
        				-> where('userfiles.userId','=',$user_id)
        				-> where('files.filename','like','images.csv') -> first();
	    Log::info('[SEARCH IMAGES]: project=>' . json_encode($project));
	    $csvfile = fopen(public_path().$project->filepath,"w");

        try
	    {
	        $images = DB::table('images')
	        				->join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                      		->join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                      		->whereIn('classlabel.title', $query_array)
	                    	->chunk(5000, function($images) use (&$csvfile){
                                        Log::info(count($images));
                                        foreach($images as $image)
                                        {
                                        	$path = public_path().$image->uri.$image->filename;
											fputcsv($csvfile, [$path]);
										}
                                    });

            // $images = DB::select(DB::RAW("SELECT * FROM images INNER JOIN imageclasslabels ON images.id=imageclasslabels.image_id INNER JOIN classlabel ON imageclasslabels.class_id=classlabel.id WHERE classlabel.title = 'wedding';"));

         	Log::info('[SEARCH IMAGES]: images=>' . count($images));
	    }
	    catch(\Exception $e)
	    {
	        $errorMessage = 'Caught exception: ' . $e->getMessage();

	        Log::info('[Exception Query]: '.$errorMessage);
	    }

        // return to the start of the stream
        rewind($csvfile);

        //close the csvfile
        fclose($csvfile);

    }

    /**
    * Check if rootDirectory is already there or not. If not then it will create
    *
    * @return $dir
    */
    function createProjectsDirectory ()
    {
		$main_dir = '/userprojects';
		// $dir = public_path().$main_dir;
		$dir  = $this->core->makeFolder(public_path().$main_dir);
		// if (!file_exists($dir))
		// {
		// 	mkdir($dir, 0777, true);
		// }

		return $main_dir;
    }

}
