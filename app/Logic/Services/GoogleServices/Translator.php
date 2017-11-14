<?php

namespace App\Logic\Services\GoogleServices;

use Auth;
use App\Models\Youtube;
use App\Models\GoogleImage;
use App\Models\Country;
use App\Models\Classlabel;
use App\Models\ImageClasslabels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

use App\Logic\Services\DBService;
use App\Logic\Services\GoogleServices\GoogleImages;

use Log;

class Translator
{

    public function __construct(DBService $dbService, GoogleImages $gImage)
    {
        $this->dbService = $dbService;
        $this->gImage = $gImage;

        $this->DEVELOPER_KEY = 'AIzaSyATqtxBdJCR-E_J5LcxfD9wSXelFxDHg2g';
        $Project_ID = 'our-shield-143614';
        $Project_number = '686945767552';

        // Example URL for custom search
        // https://www.googleapis.com/customsearch/v1?q=satellite+images+of+winston+cyclon&cx=002044490081782989713%3Ac0qzbrcxfcg&key=AIzaSyDQ0S2EosFxb_Le51vuVXHt2S3PIW9HCVw

        $this->client = new \Google_Client();
        $this->client->setApplicationName("Translator");
        $this->client->setDeveloperKey($this->DEVELOPER_KEY);

        $this->serviceTranslator = new \Google_Service_Translate($this->client);
    }

    public function crawl( $tags )
    {

      // $q = "earthquake in pakistan";

        foreach ($tags as $value)
        {
          $name = explode(' ', $value);
          $country = null;
          $countryToSearch = $name[$i];
          for ($i=$name.length; $i > 1 ; $i--) {
            $found = Country::where('Country','=',$countryToSearch)->get();
            if($found){
              break;
            } else {
              $countryToSearch = $name[$i].' '.$countryToSearch;
            }
          }
          $translatedText = $this->getTextTraslate($value, $this->DEVELOPER_KEY);
          $this->gImage->getImagesFromGoogle($translatedText);
        }
    }

    function getTextTraslate($q, $key)
    {

      $translate = $this->serviceTranslator;

        try {
          // Call the search.list method to retrieve results matching the specified
          // query term.

          $response = $translate->detections->listDetections($q);
          Log::info('[Google Translator][ListDetection]: ' . json_encode($response));

          $response = $translate->languages->listLanguages();
          Log::info('[Google Translator][ListLanguage]: ' . json_encode($response));

          $response = $translate->translations->listTranslations($q,'ur');

          // , array(
          //   'q' => $q,
          //   'source' => 'en',
          //   'target' => 'ur'
          // )

          Log::info('[Google Translator][Translations]: ' . json_encode($response));

          $request = 'https://www.googleapis.com/language/translate/v2?key='.$key.'&source=en&target=ur&q='.urlencode($q);
          // var_dump($request);

          // $curl_handle=curl_init();
          // curl_setopt($curl_handle, CURLOPT_URL,$request);
          // curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
          // curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
          // curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Translator');
          // $request = curl_exec($curl_handle);
          // curl_close($curl_handle);

          $response = file_get_contents($request);

          // var_dump($request);

          $data = json_decode($response);

          // var_dump($data);

          $translatedText = $data->data->translations[0]->translatedText;
          Log::info('[Response] '.json_encode($translatedText));
          return $translatedText;

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