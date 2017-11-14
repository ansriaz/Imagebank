<?php

namespace App\Logic\Services\GoogleServices;

use Auth;
use App\Models\Youtube;
use App\Models\GoogleImage;
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

class YoutubeCrawl
{

    public function __construct(DBService $dbService)
    {
        $this->dbService = $dbService;

        $DEVELOPER_KEY = 'AIzaSyDQ0S2EosFxb_Le51vuVXHt2S3PIW9HCVw';
        // BrowserKey = AIzaSyDQ0S2EosFxb_Le51vuVXHt2S3PIW9HCVw
        // ServerKey = AIzaSyB9A7WIQ9tFlgFembyD0CFpZWtRpdpQLdw
        // Client ID = 407143739384-grm0cdfb4ei2tcufknq54bntcdife3ij.apps.googleusercontent.com
        // CX = 002044490081782989713:c0qzbrcxfcg

        // Example URL for custom search
        // https://www.googleapis.com/customsearch/v1?q=satellite+images+of+winston+cyclon&cx=002044490081782989713%3Ac0qzbrcxfcg&key=AIzaSyDQ0S2EosFxb_Le51vuVXHt2S3PIW9HCVw

        /* Get config variables */
        // $client_id = Config::get('google.client_id');
        // $service_account_name = Config::get('google.service_account_name');
        // $key = Config::get('google.api_key');//you can use later

        // $key_file_location = base_path() . Config::get('google.key_file_location');

        $this->client = new \Google_Client();
        $this->client->setApplicationName("Youtube");
        $this->client->setDeveloperKey($DEVELOPER_KEY);
        // $this->client->setClientId('407143739384-grm0cdfb4ei2tcufknq54bntcdife3ij.apps.googleusercontent.com');
        // $this->client->setClientSecret('fasb61izXVwKhTSgqM-5_G5j');
        $this->serviceYoutube = new \Google_Service_YouTube($this->client);

        $this->serviceSearch = new \Google_Service_Customsearch($this->client);
    }

    public function crawl( $tags )
    {

      $q = 'earthquack';

      // $tags = ['landslide in sri lanka 2015','Cyclone Winston','Cyclone Roanu','Landslides Kegalle district Sri Lanka','earthquake in pakistan','pakistan kashmir earthquake','8 October pakistan earthquake','kashmir earthquake 2005','Pakistan earthquake from satellite','Nasa images of  Pakistan earthquake','8 October  Pakistan earthquake','muzaffarabad earthquake','2005 earthquake  AJK','pakistan winter earthquake','UN aid in pakistan kashmir earthquake','winston cyclone fiji','UN in winston cyclone fiji','cyclone winston 2016','destruction cyclone winston','cyclone  fiji','Cyclone Roanu Bangladesh','European Commission in Cyclone Roanu Bangladesh','Roanu storm ','Landslide in Badulla'];

        foreach ($tags as $value)
        {
          $this->getYoutubeData($value);
          // $this->getGoogleCustomSearchData($value);
        }
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

          $searchResponse = $customsearch->cse->listCse('id,snippet', array(
            'q' => $q,
            'cx' => '002044490081782989713:c0qzbrcxfcg',
            'searchType' => 'image',
            'fileType' => 'png,jpg',
            'start' => $startIndex
          ));

          $images = [];

          $count = $searchResponse['searchInformation']['totalResults'];

          $totalPages = $count / 10;
          Log::info('totalPages: '.$totalPages);
          if($startIndex == 1) {
            $totalResults = $count;
          }

          $obj = GoogleImage::all()->count();
          $id = $obj + 1;
            foreach ($searchResponse['items'] as $searchResult)
            {
              // array_push($images,$searchResult);

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

      $obj = GoogleImage::where('link',$searchResult['link'])->first();
      // Log::info($obj);

      $imgType = $searchResult['mime'];
      $file_ext = '.jpg';
      if (strpos($imgType, 'jpeg') !== false) {
          $file_ext = '.jpg';
      } else {
          $file_ext = '.png';
      }

      if(is_null($obj))
      {
        try {
            $uploadSuccess = $this->original( $filename.$file_ext, $searchResult['link'], $dir_path );
        } catch (Exception $e) {
            Log::info('Caught exception: ',  $e->getMessage(), "\n");
            return;
        }

        $gImage = new GoogleImage;
        $gImage->kind = $searchResult['kind'];
        $gImage->title = $searchResult['title'];
        $gImage->filename = $filename.$file_ext;
        $gImage->link = $searchResult['link'];
        $gImage->url = $searchResult['mime'];
        // $gImage->description = $searchResult['pagemap']['webpage'][0]['description'];
        $gImage->uri = $dir_path;
        $gImage->source = 'google custom search';
        // $gImage->cse_image = $searchResult['pagemap']['cse_image'][0]['src'];
        $gImage->snippet = $searchResult['snippet'];
        $gImage->classlabel = $classlabel;

        $gImage->save();
      }
    }

    function original( $filename, $url, $dir_path )
    {

        ini_set('memory_limit', '512M');
        Log::info($url);
        // Log::info($dir_path);
        try {
           $data = file_get_contents($url);
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

}