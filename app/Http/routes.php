<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});
Route::get('/get', 'WelcomeController@GetReport'); //Get report about the datasets, images and classes of datasets

// Laravel Routing for authentication / login of user
Route::auth();

// If new user request in not yet reviewed or confirmed
Route::get('/notallowed', function () {
	return view('/errors/account_not_reviewed');
});

// Home Page after user logs in
Route::get('/home', 'HomeController@index');
Route::get('/getReport', 'HomeController@getReport'); //Get report about the datasets, images and classes of datasets

Route::group(['middleware' => 'App\Http\Middleware\AdminMiddleware'], function()
{
    Route::get('/admin', function()
    {
        return redirect('/admin');
    });
});

// About Page - In progress
Route::get('/about', function(){
    return view('/pages/about');
});

// Contact Page - In progress
Route::get('/contact', function(){
    return view('/pages/contact');
});

// Privacy and Copyright Section
Route::get('/terms', function(){
    // return view('/pages/copyright');
    return view('pages.terms');
});

// Welcome Page
Route::get('/register/thankyou', function () {
    return view('/pages/thankyou/registration_done');
});

/**
	// This section contains all the routes for Admin Controller and functions.
	// View and routes for Admin
*/
// Show Admin View main page
Route::get('/admin', 'AdminController@Index');
Route::get('/admin/home', 'AdminController@Index');
// View for New Images that crawler downloaded from web
Route::get('/admin/newimages', 'AdminController@NewImages');
// Get Edit images view
Route::get('/admin/geteditimages', 'AdminController@GetEditImages');
// Editing images downloaded by crawler
Route::get('/admin/editimages', 'AdminController@EditImages');
// Editing One image downloaded by crawler
Route::post('/admin/editimage', 'AdminController@EditImageClassAndDataset');
// Confirm new registered user
Route::get('/confirm_user/{user_id}', 'AdminController@ConfirmUser');
// Get the current class of image
Route::get('/admin/getCurrentClassOfImage', 'AdminController@GetCurrentClassOfImage');
// Get report of Classes with images counts of each class
Route::get('/admin/getClassReport', 'AdminController@GetReportOfClasses');
/**
	// End Admin Controller
*/

/**
	// This section contains all the routes for uploading images and videos. 
	// View and routes for Uploading images and videos
*/
// For uploading images - DropZone
Route::get('/upload', ['as' => 'upload', 'uses' => 'UploadController@getUpload']);
Route::post('upload', ['as' => 'upload-post', 'uses' =>'UploadController@postUpload']);
Route::post('upload/delete', ['as' => 'upload-remove', 'uses' =>'UploadController@deleteUpload']);

// For uploading video - DropZone
Route::get('/video/upload', 'UploadVideoController@getUploadVideo');
Route::post('/video/upload', 'UploadVideoController@postUploadVideo');
Route::post('/video/upload/delete/{file_id}', 'UploadVideoController@deleteUploadVideo');

// This is not included anywhere and this part is not completed.
// Upload Dataset (zip file - complete dataset)
Route::get('/uploadDataset', 'UploadController@getUploadDataset');
Route::post('/postDataset', 'UploadController@uploadDataset');
/**
	// End Upload Video and Image Controller
*/

/**
	// This section contains all the routes for Setting and details of dataset uploaded by individual user.
	// View and routes for User's setting and dataset and its detail
*/
// User's dashboard - Update Info, Change Password and Check datasets
Route::get('/dashboard', 'UserController@index');
Route::post('/updateinfo', 'UserController@updateInfo');
Route::post('/updatepassword', 'UserController@updatePassword');
Route::get('/getDatasets', 'UserController@getDatasets');
Route::get('/details/{type}', 'UserController@getClasslabelsOfDataset');
Route::get('/getimages', 'UserController@getDatasetByClasslabels');

Route::get('/getvideos', 'UserController@getVideos');
/**
	// End User Controller
*/

/**
	// This section contains all the routes for downloading archives of selected or search images.
	// View and routes for Downloading and archiving
*/
// For downloading image datasts
// Route::post('/get/{filename}', ['as' => 'getfile', 'uses' =>'UploadController@download']);
Route::get('/proceedToDownload', 'DownloadController@index');
Route::get('/download', 'DownloadController@download');
Route::get('/downloadArchive/{file}', 'DownloadController@downloadArchive');
/**
	// End Download Controller
*/

/**
	// This section contains all the routes for all kind of searching.
	// View and routes for Search
*/
// Search for images via classlabels and tags
Route::get('/search', 'SearchController@index');
Route::get('/searchimages', 'SearchController@searchImages');
Route::get('/sortimages', 'SearchController@sortImages');

Route::get('/searchvideos', 'SearchController@SearchVideos');

// Advance Search
Route::get('/advanceSearch', 'SearchController@getAdvanceSearchView');
Route::get('/advanceSearchGetClasslabelOfDataset', 'SearchController@getClassLabelsOfDataset');
Route::post('/advanceSearch', 'SearchController@doAdvanceSearch');
/**
	// End Search Controller
*/

/**
	// This section contains all the routes for all functions of matlab.
	// View and routes for Matlab
*/
// Matlab
Route::get('/matlab', 'MatlabController@index');
Route::get('/addfile', 'MatlabController@addFile');
Route::post('/savefile', 'MatlabController@saveFile');
Route::post('/save', 'MatlabController@saveAll');
Route::get('/refresh', 'MatlabController@refresh');
Route::get('/runmatlab', 'MatlabController@runMatlab');
Route::get('/matlbimages', 'MatlabController@searchImagesForMatlab');
/**
	// End Matlab Controller
*/

/**
	// This section contains all the routes for all crawlers.
	// View and routes for Crawlers
*/
// Flickr Crawler - Not a cron job, you have to run it manually via this function
Route::get('/flickr', 'BaseController@startFlickrService');
/**
	// End Base Controller
*/