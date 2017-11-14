<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Redirect;
use App\User;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Logic\Services\EmailService;
use App\Logic\Services\DBService;
use App\Models\ImageClasslabels;
use App\Models\Image;
use Session;

use Log;

class AdminController extends Controller
{

	/**
     * Create a new controller instance.
     * And initiate DBService for querying from database
     * middleware('auth') is necessary for unauthorized access. 
     *
     * @return void
     */
    public function __construct(DBService $db)
    {
		$this->middleware('auth');
        $this->db = $db;
    }

    /**
     * Create a Report of database
     *
     * returns admin.home page with report
     * @return view(admin.home)
     */
	public function Index ( )
    {
		Log::info('===================ADMIN====================');
		$options = array(
            'images' => array('total'=>$this->db->getImagesCount(),
            					'user'=>$this->db->getUserImagesCount(),
            					'system'=>$this->db->getAdminImagesCount()),
            'classes' => array('total'=>$this->db->getClassesCount(),
            					'user'=>$this->db->getUserClasslabelsCount(),
            					'system'=>$this->db->getAdminClasslabelsCount()),
            'datasets' => array('total'=>$this->db->getDatasetsCount(),
            					'user'=>$this->db->getUserDatasetCount(),
            					'system'=>$this->db->getAdminDatasetCount())
        );

        Log::info('[REPORT] ' . json_encode($options));
		return view('admin.home')->with($options);
    }

    /**
     * Find the new images with latest version number and send it as page of size 40 to view
     *
     * returns admin.newimages page with images
     * @return view(admin.newimages)
     */
    public function NewImages ( )
    {
		$pageNumber = Input::get('page', 1);
        $perPage = 40;

        $version = Image::select('version')->distinct()->orderBy('version', 'desc')->get();
        Log::info('[LASTEST VERSION]: ' . $version);
        $images = Image::where('version','=',$version[0]->version)->paginate(40); //$version[0]->version
        Log::info('[NEW IMAGES]: ' . json_encode($images));
		
        return view('admin.newimages')->with('images', $images);
    }

    /**
     * Create EditImages view
     *
     * returns admin.editimages page with images
     * @return view(admin.editimages)
     */
    public function GetEditImages ( )
    {
		$version = Image::select('version')->distinct()->orderBy('version', 'desc')->get();
        Log::info('[LASTEST VERSION]: ' . $version);
        $images = Image::where('version','=',$version[0]->version)->paginate(12); //$version[0]->version

    	$data = array(
            'datasets' => $this->db->getAllDatasets(),
            'images' => $images
        );
		return view('admin.editimages')->with('responseData', $data);
    }

    /**
     * Find the new images with latest version number and send it as page of size 40 for editing
     *
     * returns admin.editimages page with images
     * @return view(admin.newimages)
     */
    public function EditImages ( )
    {
		$version = Image::select('version')->distinct()->orderBy('version', 'desc')->get();
        Log::info('[LASTEST VERSION]: ' . $version);
        $images = Image::where('version','=',$version[0]->version)->paginate(12);
        Log::info('[NEW IMAGES]: ' . json_encode($images));

        if(count($images) == 0)
        {
            return view('errors.no_image_found');
        } else
        {
			return view('admin.editimages')->with('images', $images);
        }
		return view('admin.editimages');
    }

    /**
     * Update dataset, classlabel of newly added images
     *
     * @return true/false
     */
    public function EditImageClassAndDataset ()
    {
		$dataset_id = Input::get('dataset_id');
		$image_id = Input::get('image_id');
		$class_id = Input::get('class_id');
		Log::info('[GetCurrentClassOfImage]: dataset_id=> ' . $dataset_id . " image_id=> " . $image_id . " class_id=> " . $class_id);

		$image_update = Image::where('id', $image_id)->update(['dataset_id' => $dataset_id]);
		$imageClassLabel_update = ImageClasslabels::where('image_id', $image_id)->update(['class_id' => $class_id]);
		return response()->json(["result"=>"record updated"]);
    }

    /**
     * Confirm User
     * Note: Admin's login is important to confirm it
     *
     * @return void
     */
    public function ConfirmUser ( $user_id )
    {
		$user = User::findOrFail($user_id);
		if (isset($user))
		{
			$result = User::where('id','=',$user_id)
							-> update(['confirmed' => 1]);
			if(isset($result))
			{
				$EmailService = new EmailService;
				$job = $EmailService->SendConfirmationEmail($user->id);
			}
		}
		return Redirect::to('/admin');
    }

    /**
     * Get current class of images
     *
     * @return json
     */
    public function GetCurrentClassOfImage ()
    {
		$dataset_id = Input::get('dataset_id');
		$image_id = Input::get('image_id');
		Log::info('[GetCurrentClassOfImage]: dataset_id=> ' . $dataset_id . " image_id=> " . $image_id);

		$clabels = $this->db->getClasslabelsByDatasetId($dataset_id);
		$imgClabel = ImageClasslabels::where('image_id','=',$image_id)->first();

        return json_encode(array('classes'=>$clabels, 'img'=>$imgClabel->class_id));
    }

    /**
     * Get current report of Classes
     *
     * @return json
     */
    public function GetReportOfClasses ()
    {
		$section = Input::get('section');
		Log::info('[GetReportOfClasses]: section segment of class chart=> ' . $section);
		$result = null;
		if($section == "System")
		{
			$result = ImageClasslabels::join('classlabel','classlabel.id','=','imageclasslabels.class_id')
							// -> join('user_datasets','classlabel.dataet_id','=','user_datasets.id')
                            -> selectRaw('classlabel.title as title, count(*) as images')
                            -> where('classlabel.dataset_id','=','1')
                            -> groupBy('imageclasslabels.class_id')
                            -> get();
		} else {
			$result = ImageClasslabels::join('classlabel','classlabel.id','=','imageclasslabels.class_id')
                            -> selectRaw('classlabel.title as title, count(*) as images')
                            -> where('classlabel.dataset_id','!=','1')
                            -> groupBy('imageclasslabels.class_id')
                            -> get();
		}
        // Log::info('[CLASSES WITH IMAGES]: ' . $result);
        return $result;
    }
}
