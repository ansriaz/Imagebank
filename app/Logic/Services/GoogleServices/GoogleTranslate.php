<?php

namespace App\Logic\Services\GoogleServices;

use Auth;
use App\Models\Youtube;
use App\Models\GoogleImage;
use App\Models\Country;
use App\Models\Image;
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
use DB;

class GoogleTranslate
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

    	$a = 0;
        foreach ($tags as $value)
        {
        	  Log::info('VALUE [Google Translator]: "' . $value . '"');
          	$name = explode(' ', $value);
          	$country = null;
          	$countryToSearch = $name[count($name)-1];
          	for ($i=count($name)-2; $i >= 0 ; $i--) {
            		// $searchItem = preg_replace('/\v(?:[\v\h]+)/', '', $countryToSearch);
            		Log::info('WORD [Google Translator]: "' . $countryToSearch . '"');
            		// $country = DB::select( DB::raw("SELECT * FROM country WHERE country = '". $countryToSearch ."'") );
            		// var_dump($country);
              	$country = Country::where('country','=',$countryToSearch)->first();
              	// Log::info('Country [Google Translator]: ' . json_encode($country));
              	if(isset($country) && !is_null($country))
              	{
              		  Log::info($a++ . ' Country [Google Translator]: ' . json_encode($country->country));
                		break;
              	}
              	$countryToSearch = $name[$i].' '.$countryToSearch;
            }
            if(!is_null($country))
            {
            		Log::info('Found [Google Translator]: ' . json_encode($country->languages));
            		$languages = explode('"', $country->languages);
            		$codes = \Config::get('languages.code');
                // Log::info('[languages.code] ' . json_encode($codes));
            		foreach ($languages as $lang)
            		{
                  // Log::info('[language] ' . $lang);
            			if($lang != 'zh-CN' && $lang != 'zh-TW')
            			{
  	          			if(strpos($lang, '-') !== false)
  	          			{
  	          				$lang = explode('-', $lang)[0];
  	          			}
            			}
                  // foreach ($codes as $value) {
                  //     if($lang == $value){
                  //         Log::info('[lang found]: ' . $lang);
                  //     } else {
                  //         Log::info('[lang]: ' . $lang . ' [code] ' . $value);
                  //     }
                  // }
            			if($lang != 'en' && in_array($lang, $codes))
            			{
              				Log::info('[lang found]: ' . $lang);
              				$translatedText = $this->getTextTraslate($value, $lang, $this->DEVELOPER_KEY);
                      $this->gImage->getImagesFromGoogle($translatedText);
            			}
            		}
          	}
            // break;
        }
    }

    function getTextTraslate($q, $langTo, $key)
    {

      $translate = $this->serviceTranslator;

        try {
          // Call the search.list method to retrieve results matching the specified
          // query term.

          // $response = $translate->detections->listDetections($q);
          // Log::info('[Google Translator][ListDetection]: ' . json_encode($response));

          // $response = $translate->languages->listLanguages();
          // Log::info('[Google Translator][ListLanguage]: ' . json_encode($response));

          // $response = $translate->translations->listTranslations($q,$langTo);

          // , array(
          //   'q' => $q,
          //   'source' => 'en',
          //   'target' => 'ur'
          // )

          // Log::info('[Google Translator][Translations]: ' . json_encode($response));

          $request = 'https://www.googleapis.com/language/translate/v2?key='.$key.'&source=en&target='.$langTo.'&q='.urlencode($q);
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
          var_dump($q);
          var_dump($translatedText);
          return $translatedText;

        } catch (Google_Service_Exception $e) {
          Log::info($e->getMessage());
        } catch (Google_Exception $e) {
          Log::info($e->getMessage());
        }
    }
}