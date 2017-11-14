<?php

namespace App\Logic\Services\GoogleServices;

use Auth;
use App\Models\Youtube;
use App\Models\Image;
use App\Models\UserDatasets;
use App\Models\Classlabel;
use App\Models\ImageClasslabels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

use App\Logic\Services\DBService;

use Log;

class GoogleImages
{
    public function __construct(DBService $dbService)
    {
        $this->dbService = $dbService;

        $DEVELOPER_KEY = 'AIzaSyATqtxBdJCR-E_J5LcxfD9wSXelFxDHg2g';

        $this->client = new \Google_Client();
        $this->client->setApplicationName("GoogleJord");
        $this->client->setDeveloperKey($DEVELOPER_KEY);

        $this->serviceSearch = new \Google_Service_Customsearch($this->client);
    }

    public function crawl( $tags )
    {

        $q = 'earthquack';

        foreach ($tags as $value)
        {
          $this->getGoogleCustomSearchData($value);
        }
    }

    public function getImagesFromGoogle( $tag )
    {
          Log::info('[GetImagesFromGoogle] ' . $tag);
          var_dump($tag);
          $this->getGoogleCustomSearchData($tag);
    }

    function getGoogleCustomSearchData($q)
    {

      $customsearch = $this->serviceSearch;

        $startIndex = 1;
        $totalResults = 0;
        $pageNo = 1;
        try {
            // Call the search.list method to retrieve results matching the specified
            // query term.
            do {

                Log::info('[q] ' . $q);

                $searchResponse = $customsearch->cse->listCse('id,snippet', array(
                  'q' => $q,
                  'cx' => '002044490081782989713:c0qzbrcxfcg',
                  'searchType' => 'image',
                  'fileType' => 'png,jpg',
                  // 'imgType' => 'photo',
                  'filter' => '0',
                  'rights' => 'cc_publicdomain',
                  'start' => $startIndex
                ));

                $images = [];

                // var_dump($searchResponse);
                Log::info('[Response] ' . json_encode($searchResponse));

                $count = $searchResponse['searchInformation']['totalResults'];

                $totalPages = $count / 10;
                Log::info('totalPages: '.$totalPages);
                if($startIndex == 1) {
                  $totalResults = $count;
                }

                $obj = Image::all()->count();
                $id = $obj + 1;
                foreach ($searchResponse['items'] as $searchResult)
                {
                    // array_push($images,$searchResult);

                    // Log::info('[image] ' . json_encode($searchResult));

                    $this->saveImage($searchResult, $q, $id);
                    $id++;
                }

                // Log::info($images);
                $startIndex += 10;

            } while ($pageNo < $totalPages && $startIndex < 100);

        } catch (Google_Service_Exception $e) {
          Log::info($e->getMessage());
        } catch (Google_Exception $e) {
          Log::info($e->getMessage());
        }
    }

    function saveImage($searchResult, $classlabel, $id)
    {
        $dir = '/images/google_images/';
        $destinationPath = public_path() . $dir;
        // $dir_path = '/images/google_images/'.$classlabel.'/';
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath);
        }
        $dir_path = '/images/google_images/'.$classlabel.'/';

        $filename = preg_replace('/\s+/', "_", $classlabel);
        $filename = $filename.'_'.$id;
        // Log::info($searchResult['link']);

        $obj = Image::where('link',$searchResult['link'])->first();
        // Log::info($obj);

        $imgType = $searchResult['mime'];
        // if(is_null($imgType))
        // {
            $file_ext = '.jpg';
            Log::info('[imageType]: ' . $imgType);
            if (strpos($imgType, 'jpeg') !== false) {
                $file_ext = '.jpg';
            } else {
                $file_ext = '.png';
            }
        // } else {
        //     $file_ext = explode('.', $searchResult['link']);
        // }

        // if(is_null($imgType)){
        if(is_null($obj))
        {
            try {
                $uploadSuccess = $this->original( $filename.$file_ext, $searchResult['link'], $dir_path );
            } catch (Exception $e) {
                Log::info('Caught exception: ',  $e->getMessage(), "\n");
                return;
        }

        // Get the latest version number
        $version = Image::select('version')->distinct()->orderBy('version', 'desc')->get();
        // Log::info('[LASTEST VERSION]: ' . $version);
        $newVersion = 0;
        if(isset($version) && count($version) > 0)
        {
            // $currentVersion = Image::where('version','=',$version[0]->version)->first(); //$version[0]->dataset_id
            // Log::info('[NEW VERSION]: ' . json_encode($currentVersion));
            // $newVersion = ($currentVersion->version) ? $currentVersion->version +1 : 1;
            $newVersion = $version[0]->version + 1;
            // Log::info('[NEW VERSION]: ' . json_encode($newVersion));
        }

        $dataset_id = $this->addDatasetOfImages("google_images",'images added by google image crawler');
        $classlbl = $this->updateClassLabels( $classlabel , $dataset_id );

        $sessionImage = new Image;
        $sessionImage->title = $searchResult['title'];
        $sessionImage->filename = $filename.$file_ext;
        $sessionImage->name = $searchResult['title'];
        $sessionImage->uri = $dir_path;
        $sessionImage->tags = $searchResult['snippet'];
        $sessionImage->link = $searchResult['link'];
        $sessionImage->source = 'google custom search';
        $sessionImage->user_id = 1;
        $sessionImage->version = $newVersion;
        $sessionImage->owner = 1;
        $sessionImage->dataset_id = $dataset_id;
        $sessionImage->save();
        // }

      }
    }

    function original( $filename, $url, $dir_path )
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
}
