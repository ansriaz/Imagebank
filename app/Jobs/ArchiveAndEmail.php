<?php

namespace App\Jobs;

use Auth;
use App\User;
use Mail;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\Image;
use App\Models\ImageClasslabels;
use App\Models\Classlabel;

use App\Logic\Services\DBService;
use DB;
use Log;

class ArchiveAndEmail extends Job implements ShouldQueue, SelfHandling
{
    use InteractsWithQueue, SerializesModels;

    // run this from command line to before starting the job.
    // php artisan queue:listen --timeout=0

    protected $search;
    protected $datasets;
    protected $dataset_title;
    protected $archive_file_name;
    protected $user_id;
    protected $dbService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $search, $datasets, $dataset_title = null, $archive_file_name, $user_id)
    {
        Log::info('Control is in ArchiveAndEmail Job');
        Log::info($search);
        Log::info(json_decode($datasets));
        Log::info($dataset_title);
        Log::info($archive_file_name);

        $this->search = $search;
        $this->datasets = json_decode($datasets);
        $this->dataset_title = (!isset($dataset_title)) ? null : $dataset_title;
        $this->archive_file_name = $archive_file_name;
        $this->user_id = $user_id;
        $this->dbService = new DBService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle( )
    {
        Log::info("Archive Started");

        $url = $this->createArchive();

        Log::info($url);

        $user = User::findOrFail($this->user_id);

        $data = array(
            'download' => $url,
            'name' => $user->name
        );

        Mail::send('emails.download', $data, function ($message) use ($user) {

            $message->from( \Config::get('constants.ADMIN_EMAIL'), \Config::get('constants.WEBSITE_NAME') );

            $message->to($user->email)->subject( \Config::get('constants.WEBSITE_NAME').': Your archive is ready.');

        });
    }

    public function fire($job, $data)
    {
        $job->delete();
    }

    /**
     * Archives the array of images.
     *
     * @return
     */
    protected function createArchive( )
    {
        $images = [];

        // ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '4095M');

        $zip = new \ZipArchive;

        $zip_file = public_path() .'/archives/'. $this->archive_file_name;
        Log::info('Zip file destination: ' . $zip_file);

        try {

            if ($zip -> open($zip_file, \ZipArchive::CREATE) === FALSE)
                throw new \Exception('Could not create zip file!');
    
            foreach ($this->datasets as $value)
            {
                Log::info("value: ".$value);
                $folderName = $value;

                if($this->search === 1)
                {
                    if($zip->addEmptyDir($folderName)) {
                        Log::info('Created a new root directory');
                    } else {
                        Log::info('Could not create the directory');
                    }
                    // create a temporary file
                    $csvfile = fopen("php://temp/maxmemory:104857600","w");

                    // get images and add them in archive
                    // $count = ImageClassLabels::join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                    //                         -> where('classlabel.title', 'like', '%'.$value.'%')->count();
                    // Log::info('[SEARCH IMAGES]: images count => ' . $count);
                    
                    // $chunk = 5000;
                    // $turns = ceil($count / $chunk);

                    $chunks = DB::table('images')
                                    -> join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                                    -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                                    -> where('classlabel.title', 'like', '%'.$value.'%')
                                    -> orWhere('tags', 'like', '%'.$value.'%')
                                    -> chunk(5000, function($images) use (&$zip, &$folderName, &$csvfile){
                                        Log::info(count($images));
                                        $this->addImagesToArchive($zip, $folderName, $images, $csvfile);
                                    });

                    // Log::info($images);

                    // $startPoint = 0;
                    // $i = 1; 
                    // while($i <= $turns)
                    // {
                        // $images = DB::table('images')
                        //                 ->join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                        //                 ->join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                        //                 ->where('classlabel.title', 'like', '%'.$value.'%')
                        //                 ->limit($chunk)->offset($startPoint)->get();

                        // Log::info($folderName.': '.count($images));
                        // $this->addImagesToArchive($zip, $folderName, $images, $csvfile);

                        // Log::info('startPoint ' . $startPoint . " i ". $i ." chunk " . $chunk);
                        // $startPoint = $i * $chunk;
                        // $i++;
                    // }

                    // To add the csv data in archive
                    // return to the start of the stream
                    rewind($csvfile);
                    // add the in-memory file to the archive, giving a name
                    $zip->addFromString( $folderName.'/'.$folderName.'.csv', stream_get_contents($csvfile) );
                    //close the file
                    fclose($csvfile);
                } else {

                    $label = $this->dbService->getUserDatasetByName($folderName);

                    Log::info('[DATASET]: '.$label);

                    if(!is_null($label)) {

                        if($zip->addEmptyDir($folderName)) {
                            Log::info('Created a new root directory');
                        } else {
                            Log::info('Could not create the directory');
                        }
                        // create a temporary file
                        $csvfile = fopen("php://temp/maxmemory:104857600","w");

                        // Log::info($this->user_id);
                        // Log::info($label->id);
                        $images = Image::where('user_id',$this->user_id)
                                        -> where('dataset_id',$label->id)
                                        -> chunk(5000, function($images) use (&$zip, &$folderName, &$csvfile){
                                            Log::info(count($images));
                                            $this->addImagesToArchive($zip, $folderName, $images, $csvfile);
                                        });

                        // Log::info($images);

                        // To add the csv data in archive
                        // return to the start of the stream
                        rewind($csvfile);
                        // add the in-memory file to the archive, giving a name
                        $zip->addFromString( $folderName.'/'.$folderName.'.csv', stream_get_contents($csvfile) );
                        //close the file
                        fclose($csvfile);

                    } else {

                        if($zip->addEmptyDir($folderName)) {
                            Log::info('Created a new root directory');
                        } else {
                            Log::info('Could not create the directory');
                        }
                        // create a temporary file
                        $csvfile = fopen("php://temp/maxmemory:104857600","w");

                        Log::info('[DATASET-TITLE]: '.$this->dataset_title);
                        $dataset_id = $this->dbService->getUserDatasetByName($this->dataset_title)->id;
                        Log::info('[DATASET-label]: '.$label);

                        $label = $this->dbService->getClassIdByTitleAndDatasetId($folderName, $dataset_id);
                        Log::info($label);

                        $images = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                                      -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                                      -> where('images.user_id',$this->user_id)
                                      -> where('imageclasslabels.class_id',$label->id)
                                      -> select('images.*')
                                      -> chunk(5000, function($images) use (&$zip, &$folderName, &$csvfile){
                                            Log::info(count($images));
                                            $this->addImagesToArchive($zip, $folderName, $images, $csvfile);
                                        });

                        // To add the csv data in archive
                        // return to the start of the stream
                        rewind($csvfile);
                        // add the in-memory file to the archive, giving a name
                        $zip->addFromString( $folderName.'/'.$folderName.'.csv', stream_get_contents($csvfile) );
                        //close the file
                        fclose($csvfile);
                    }

                }
            }

            if(!$zip)
                die("Error: zip file lost");
            $res = $zip->close();
            Log::info("Closed with: " . ($res ? "true" : "false"));
            return $this->archive_file_name;
        } catch (\Exception $e) {
            Log::info('Caught exception: '.  $e->getMessage(). "\n");
        }
    }

    // Add images to archive and get the CSV data return it back.
    function addImagesToArchive($zip, $folderName, $images, $csvfile)
    {
        $obj = ['name', 'title', 'description', 'tags', 'contributorlocation', 'link'];
        fputcsv($csvfile, $obj);

        foreach($images as $image)
        {
            $file = public_path().$image->uri.$image->filename;
            // Log::info($image->id.' file to download: '. $file);
            $zip->addFile($file, $folderName.'/'.$image->filename);

            // $class_title = Classlabel::join('imageclasslabels', 'classlabel.id','=','imageclasslabels.class_id')
            //                           -> join('images', 'imageclasslabels.image_id','=','images.id')
            //                           -> where('images.id',$image->id)
            //                           -> select('classlabel.*')
            //                           -> first();

            // Log::info($class_title);

            // $obj = [$image->name, $class_title->title, $image->description, $image->tags, $image->contributorlocation, $image->link];
            $obj = [$image->name, $folderName, $image->description, $image->tags, $image->contributorlocation, $image->link];
            // Log::info($obj);
            fputcsv($csvfile, $obj);
        }
    }
}
