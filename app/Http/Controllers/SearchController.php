<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Logic\Services\DBService;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Image;
use App\Models\Video;
use App\Models\VideoClasses;
use Session;

use Log;
use DB;

class SearchController extends Controller
{

    var $searchTerm = null;
	public function __construct(DBService $dbService)
    {
        $this->dbService = $dbService;
    }

    public function index()
    {
        $query = Input::only('q');
        Log::info($query['q']);
        if(isset($query['q']))
        {
            $this->searchTerm = $query['q'];
            \Session::set('searchTerm', $query['q']);
        } else {
            $query = Input::only('query')['query'];
            Log:info($query);
            return view('pages.searchresult')->with('query',$query);
        }

        Log::info(\Session::get('searchTerm'));

        return view('pages.searchresult')->with('q',$this->searchTerm);
    }

    public function searchImages( )
    {
		// $query = Input::only('q');
        // Log::info(\Session::get('searchTerm'));
        // Log::info($this->searchTerm);

        DB::connection()->disableQueryLog();
        $searchTerm = null;
        if(isset($keywords))
        {
            $searchTerm = $keywords;
        } else {
            $searchTerm = \Session::get('searchTerm');
        }
        Log::info('[SEARCH_QUERY]: '.json_encode($searchTerm));

        if( isset($searchTerm['query']) || isset($searchTerm['dataset']) )
        {
            $formInputs = $searchTerm;
            $query = $searchTerm['query'];
            try {
                if(!is_null($query) && isset($query))
                {
                    $pageNumber = Input::get('page', 1);
                    $perPage = 40;

                    $query = preg_replace('/;/', '', $query);

                    $results = DB::select(DB::raw($query)); //.' LIMIT 0, 1000;'
                    // Log::info($results);

                    $imagesPerPages = [];
                    foreach ($results as $image) {
                        array_push($imagesPerPages, (array)$image);
                    }

                    $slice = array_slice($imagesPerPages, $perPage * ($pageNumber - 1), $perPage);
                    // $images = Paginator::make($slice, count($imagesPerPages), $perPage);
                    $images = new LengthAwarePaginator($slice, count($imagesPerPages), $perPage, $pageNumber);

                    if(count($images) == 0)
                    {
                        return view('errors.no_image_found');
                    } else
                    {
                        $data = array(
                            'images' => $images,
                            'query' => $query
                        );
                        // Log::info($data);
                        return view('fragment.album')->with('responseData', $data);
                    }

                } else {
                    $dataset_id = $this->dbService->getUserDatasetByName($formInputs['dataset'])->id;
                    $class_id = $this->dbService->getClassIdByTitle($formInputs['classlabel'])->id;

                    $dateFrom = $formInputs['dateFrom'];
                    $dateTo = $formInputs['dateTo'];
                    $date_query = "";
                    $dateFrom = strtotime($formInputs['dateFrom']);
                    $dateFrom = date('Y-m-d H:i:s',$dateFrom);
                    $dateTo = strtotime($formInputs['dateTo']);
                    $dateTo = date('Y-m-d H:i:s',$dateTo);
                    if(!is_null($dateFrom) && !is_null($dateTo))
                    {
                        $images = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                                  -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                                  -> select('images.*')
                                  -> where('images.dataset_id', $dataset_id)
                                  -> where('imageclasslabels.class_id',$class_id)
                                  -> where('images.date_taken','>=',$dateFrom)
                                  -> where('images.date_taken','<=',$dateTo)
                                  -> paginate(40);
                    } else {
                        if(!is_null($dateFrom))
                        {
                            $images = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                                      -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                                      -> select('images.*')
                                      -> where('images.dataset_id', $dataset_id)
                                      -> where('imageclasslabels.class_id',$class_id)
                                      -> where('images.date_taken','>=',$dateFrom)
                                      -> paginate(40);
                        }
                        else if(!is_null($dateTo))
                        {
                            $images = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                                      -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                                      -> select('images.*')
                                      -> where('images.dataset_id', $dataset_id)
                                      -> where('imageclasslabels.class_id',$class_id)
                                      -> where('images.date_taken','<=',$dateTo)
                                      -> paginate(40);
                        }
                    }
                    Log::info('[ADVANCE SEARCH]: ' . count($images));

                    if(count($images) == 0)
                    {
                        return view('errors.no_image_found');
                    } else
                    {
                        $data = array(
                            'images' => $images,
                            'search' => ''
                        );

                        // Log::info($data);
                        return view('fragment.album')->with('responseData', $data);
                    }
                }
            }
            catch (\Illuminate\Database\QueryException $e) {
                return json_encode(['danger' => "MySQL Error: " . $e->getMessage()]);
            }
        } else {
            $query_param = preg_replace('/\s+/', '', $searchTerm);

            $query_array = null;

            if ($query_param != '') {
                $query_array = explode(',', $query_param);
                // Log::info($query_array);
            }

            $images = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                          -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                          -> whereIn('classlabel.title', $query_array)
                          -> orWhereIn('tags', $query_array)
                          -> paginate(40);

            if(count($images) == 0)
            {
                return view('errors.no_image_found');
            } else
            {
                $data = array(
                    'images' => $images,
                    'q' => $query_param
                );

                // Log:info($data);

                return view('fragment.album')->with('responseData', $data);
            }
        }

    }

    public function SearchVideos( )
    {
        // $query = Input::only('q');
        // Log::info(\Session::get('searchTerm'));
        // Log::info($this->searchTerm);

        $searchTerm = null;
        if(isset($keywords))
        {
            $searchTerm = $keywords;
        } else {
            $searchTerm = \Session::get('searchTerm');
        }
        Log::info('[SEARCH_QUERY]: '.json_encode($searchTerm));

        if( isset($searchTerm['query']) || isset($searchTerm['dataset']) )
        {
            $formInputs = $searchTerm;
            $query = $searchTerm['query'];
            try {
                if(!is_null($query) && isset($query))
                {
                    $pageNumber = Input::get('page', 1);
                    $perPage = 40;

                    $query = preg_replace('/;/', '', $query);

                    $results = DB::select(DB::raw($query)); //.' LIMIT 0, 1000;'
                    // Log::info($results);

                    $videosPerPages = [];
                    foreach ($results as $video) {
                        array_push($videosPerPages, (array)$video);
                    }

                    $slice = array_slice($videosPerPages, $perPage * ($pageNumber - 1), $perPage);
                    $videos = new LengthAwarePaginator($slice, count($videosPerPages), $perPage, $pageNumber);

                    if(count($videos) == 0)
                    {
                        return view('errors.no_video_found');
                    } else
                    {
                        $data = array(
                            'videos' => $videos,
                            'query' => $query
                        );
                        // Log::info($data);
                        return view('fragment.playlist')->with('responseData', $data);
                    }

                } else {
                    $dataset_id = $this->dbService->getUserDatasetByName($formInputs['dataset'])->id;
                    $class_id = $this->dbService->getClassIdByTitle($formInputs['classlabel'])->id;

                    $dateFrom = $formInputs['dateFrom'];
                    $dateTo = $formInputs['dateTo'];
                    $date_query = "";
                    $dateFrom = strtotime($formInputs['dateFrom']);
                    $dateFrom = date('Y-m-d H:i:s',$dateFrom);
                    $dateTo = strtotime($formInputs['dateTo']);
                    $dateTo = date('Y-m-d H:i:s',$dateTo);
                    if(!is_null($dateFrom) && !is_null($dateTo))
                    {
                        $videos = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                                  -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                                  -> select('images.*')
                                  -> where('images.dataset_id', $dataset_id)
                                  -> where('imageclasslabels.class_id',$class_id)
                                  -> where('images.date_taken','>=',$dateFrom)
                                  -> where('images.date_taken','<=',$dateTo)
                                  -> paginate(40);
                    } else {
                        if(!is_null($dateFrom))
                        {
                            $videos = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                                      -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                                      -> select('images.*')
                                      -> where('images.dataset_id', $dataset_id)
                                      -> where('imageclasslabels.class_id',$class_id)
                                      -> where('images.date_taken','>=',$dateFrom)
                                      -> paginate(40);
                        }
                        else if(!is_null($dateTo))
                        {
                            $videos = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                                      -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                                      -> select('images.*')
                                      -> where('images.dataset_id', $dataset_id)
                                      -> where('imageclasslabels.class_id',$class_id)
                                      -> where('images.date_taken','<=',$dateTo)
                                      -> paginate(40);
                        }
                    }
                    Log::info('[ADVANCE SEARCH]: ' . count($videos));

                    if(count($videos) == 0)
                    {
                        return view('errors.no_image_found');
                    } else
                    {
                        $data = array(
                            'videos' => $videos,
                            'search' => ''
                        );

                        // Log::info($data);
                        return view('fragment.playlist')->with('responseData', $data);
                    }
                }
            }
            catch (\Illuminate\Database\QueryException $e) {
                return json_encode(['danger' => "MySQL Error: " . $e->getMessage()]);
            }
        } else {
            $query_param = preg_replace('/\s+/', '', $searchTerm);

            $query_array = null;

            if ($query_param != '') {
                $query_array = explode(',', $query_param);
                // Log::info($query_array);
            }

            $videos = Video::join('video_classes', 'videos.id','=','video_classes.video_id')
                          -> join('classlabel', 'video_classes.class_id','=','classlabel.id')
                          -> whereIn('classlabel.title', $query_array)
                          -> orWhereIn('tags', $query_array)
                          -> paginate(40);

            if(count($videos) == 0)
            {
                return view('errors.no_video_found');
            } else
            {
                $data = array(
                    'videos' => $videos,
                    'q' => $query_param
                );

                // Log:info($data);

                return view('fragment.playlist')->with('responseData', $data);
            }
        }

    }

    public function sortImages ( )
    {
        // $obj = $_GET['obj'];
        // $query_array = explode(',', $obj['q']);

        $query_array = \Session::get('searchTerm');

        if (count($query_array) > 0)
        {
            // $sortBy = $obj['sortBy'];
            $sortBy = $_GET['sortBy'];
            $datasets = array();

            if ($sortBy == 'tags')
            {
                foreach ($query_array as $query) {

                    $images = $this->dbService->searchImageByTags($query);

                    Log::info(count($images));

                    $data = array(
                            'dataset' => $images,
                            'title' => $query
                        );

                    array_push($datasets, $data);
                }

                 $responseData = array(
                            'datasets' => $datasets,
                            'page' => 'search'
                        );

                return view('pages.images')->with('responseData',$responseData);

            } else if ($sortBy == 'classlabels') {

                foreach ($query_array as $query) {

                    $images = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                      -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                      -> where('classlabel.title',$query)
                      -> get();

                    // $images = array_map(function($item){
                    //     return (array) $item;
                    // },$images);

                    Log::info(count($images));

                    $data = array(
                            'dataset' => $images,
                            'title' => $query
                        );

                    array_push($datasets, $data);
                }

                $responseData = array(
                            'datasets' => $datasets,
                            'page' => 'search'
                        );

                return view('pages.images')->with('responseData',$responseData);

            } else {



            }
        }
    }

    public function getAdvanceSearchView ()
    {
        $data = array(
            'datasets' => $this->dbService->getAllDatasets()
        );
        // 'class' => $this->dbService->getAllClasses()
        return view('pages.searchadvance')->with('responseData',$data);
    }

    public function getClassLabelsOfDataset() {
        $input = Input::only('dataset');
        $dataset = $input['dataset'];
        // Log::info('[selected_dataset]: ' . $dataset);

        $clabels = $this->dbService->getClasslabelsByDataset($dataset);
        // Log::info('[CLASSLABELS]: ' . $clabels);

        return json_encode($clabels);
    }

    public function doAdvanceSearch ()
    {

        $formInputs = Input::only('query','dataset','classlabel','dateFrom','dateTo');
        $query = $formInputs['query'];
        Log:info($formInputs);

        if(!is_null($query)) {
            $this->searchTerm = $formInputs['query'];
            \Session::set('searchTerm', $formInputs);
        } else {
            $this->searchTerm = $formInputs['dataset'] . $formInputs['classlabel'] . $formInputs['dateFrom'] . $formInputs['dateTo'];
            \Session::set('searchTerm', $formInputs);
        }

        return view('pages.searchresult')->with('q',$this->searchTerm);
    }
}
