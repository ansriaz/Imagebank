<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Logic\Services\FlickrService\FlickrCrawler;

use Log;

class BaseController extends Controller
{

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(FlickrCrawler $fc)
    {
        $this->fc = $fc;
    }

    public function startFlickrService() 
    {

        $photos = $this->fc->crawl();

        // $photos = $f->photos_search(array("tags"=>"brown,cow", "tag_mode"=>"any"));

        // echo $photos;
    }
}
