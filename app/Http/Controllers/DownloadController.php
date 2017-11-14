<?php

namespace App\Http\Controllers;
require dirname(__DIR__) . '../../../vendor/autoload.php';

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;
use App\User;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use DB;
use Redirect;
use Session;
use Response;

use App\Models\Image;
use App\Models\ImageClasslabels;
use App\Models\Classlabel;
use App\Logic\Services\ZipService;
use App\Logic\Services\DBService;
Use App\Jobs\ArchiveAndEmail;
use Symphony\Component\Process\Process;
use Artisan;

use ZipStream;

use Log;

class DownloadController extends Controller
{

	public function __construct(DBService $dbService)
	{
		$this->middleware('auth');
		$this->dbService = $dbService;
	}

	public function index()
	{
		$checked = Input::get('dataset');

		$dataset_title = Input::get('dataset_title');

		$searchTerm = Input::get('searchTerm');

        Log::info($checked);
        Log::info($dataset_title);

		return view('pages.downloadpage')->with('datasetToDownload',json_encode(['datasets'=>$checked, 'title'=>$dataset_title, 'searchTerm'=>$searchTerm]));
	}

	public function download()
	{
		$checked = Input::get('dataset');
		Log::info($checked);
		$checked = json_decode($checked);
		Log::info($checked->datasets);

		$search = 0;
		if(isset($checked->searchTerm)) {
			$query_array =  explode(',', $checked->searchTerm);
			Log::info($query_array);

			$checked->datasets = $query_array;
			$search = 1;
		}

		$today = date("Ymd_His");
		//Archive name
		$archive_file_name = $today.'_datasets_'.Auth::id().'.zip';

		// Artisan::queue('archive', [
		// 	'search' => $search, 'datasets' => json_encode($checked->datasets), 'dataset_title' => $checked->title, 'name' => $archive_file_name, 'user_id' => Auth::id()
		// ]);

		// log($exitCode);

		// $process = new Process('php artisan archive {'.$search.'} {'.json_encode($checked->datasets).'} {'.$checked->title.'} {'. $archive_file_name. '}'); 
		// $process->start();


		$job = (new ArchiveAndEmail($search, json_encode($checked->datasets), $checked->title, $archive_file_name, Auth::id()));
		$this->dispatch( $job );

		return view('pages.thankyou');
	}

	public function downloadArchive( $archiveName ) {
		$headers = array(
				'Content-Description: File Transfer',
				'Content-type: application/zip',
				'Content-Type: application/force-download', // some browsers need this
				'Content-Disposition: attachment; filename="$archiveName"',
				'Expires: 0',
				'Cache-Control: must-revalidate, post-check=0, pre-check=0',
				'Pragma: public'
			);

		$file = public_path().'/archives/'.$archiveName;
		return Response::download($file, $archiveName, $headers)->deleteFileAfterSend(true);
	}

    public function downloadOld()
	{

		$checked = Input::get('dataset');
		Log::info($checked);
		$checked = json_decode($checked);
		Log::info($checked->datasets);
		// Log::info(is_null($checked).'  '.is_array($checked));

		if(isset($checked->searchTerm)) {
			// $archiveName = null;
			// $images = [];
			// $today = date("m_d_Y");
			// //Archive name
			// $archive_file_name = $today.'_datasets_'.Auth::id().'.zip';

			$query_array =  explode(',', $checked->searchTerm);
			Log::info($query_array);
	  
			$checked->datasets = $query_array;
		}

		// if(is_array($checked->datasets))
		{
			$archiveName = null;
			$images = [];

			$today = date("m_d_Y");
			//Archive name
			$archive_file_name = $today.'_datasets_'.Auth::id().'.zip';

			# create a new zipstream object
			// $zip = new ZipStream\ZipStream($archive_file_name);

			$zip = new \ZipArchive;

			$zip_file = public_path().'/archives/'.$this->archive_file_name;

			if ($zip -> open($zip_file, \ZipArchive::CREATE) === TRUE) 
			{
				foreach ($checked->datasets as $value)
				{
					Log::info("value: ".$value);
					$archiveName = $value;

					if(!is_null($checked->searchTerm)) {

						$images = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                      				-> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                     		 		-> where('classlabel.title', 'like', '%'.$value.'%')
                      				-> orWhere('tags', 'like', '%'.$value.'%')
                      				-> select('images.*')
                      				-> get();
					} else {

						$label = $this->dbService->getUserDatasetByName($archiveName);
						if(!is_null($label)) {
						
							$images = Image::where('user_id',Auth::id())
								  -> where('dataset_id',$label->id)
								  -> get();
						
						} else {

							$dataset_title = $checked->title;
							$dataset_id = $this->dbService->getUserDatasetByName($dataset_title)->id;
						
							$label = $this->dbService->getClassIdByTitleAndDatasetId($archiveName, $dataset_id);
							Log::info($label);
							$images = DB::table('images')
								  -> join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
								  -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
								  -> where('images.user_id',Auth::id())
								  -> where('imageclasslabels.class_id',$label->id)
								  -> select('images.*')
								  -> get();
						}

					}

					Log::info($archiveName.': '.count($images));

					// Log::info($images);

					    if($zip->addEmptyDir($archiveName)) {
					        Log::info('Created a new root directory');
					    } else {
					        Log::info('Could not create the directory');
					    }

					$imagesData = array();

					foreach($images as $image)
					{
						$file = public_path().$image->uri.$image->filename;
						Log::info($image->id.' file to download: '. $file);
						$zip->addFile($file, $archiveName.'/'.$image->filename);

						$class_title = Classlabel::join('imageclasslabels', 'classlabel.id','=','imageclasslabels.class_id')
							  -> join('images', 'imageclasslabels.image_id','=','images.id')
							  -> where('images.id',$image->id)
							  -> select('classlabel.*')
							  -> first();

						// Log::info($class_title);

						$obj = [$image->name, $class_title->title, $image->description, $image->tags, $image->contributorlocation, $image->link];
						// Log::info($obj);
						array_push($imagesData, $obj);
					}

					// Log::info($imagesData);

					// create a temporary file
					$file = fopen("php://temp/maxmemory:1048576","w");

					// write the data to csv
					foreach ($imagesData as $fields) {
					    fputcsv($file, $fields);
					}

					// return to the start of the stream
    				rewind($file);

    				// add the in-memory file to the archive, giving a name
    				$zip->addFromString( $archiveName.'/'.$archiveName.'.csv', stream_get_contents($file) );
    				
    				//close the file 
					fclose($file);
				}

				$zip->close();
			}

			$headers = array(
				'Content-Description: File Transfer',
				'Content-type: application/zip',
				'Content-Type: application/force-download', // some browsers need this
				'Content-Disposition: attachment; filename="$archive_file_name"',
				'Expires: 0',
				'Cache-Control: must-revalidate, post-check=0, pre-check=0',
				'Pragma: public',
				// 'Content-Length': filesize($archive_file_name)
			);
			
			// App::finish(function($request, $response) use ($archive_file_name)
			// {
			//     unlink($filename);
			// });
			// return Response::download($archive_file_name);

	        return Response::download($archive_file_name, $archive_file_name, $headers)->deleteFileAfterSend(true);
		}
		// return back()->with('flash_message', 'Select album to download');
	}
}
