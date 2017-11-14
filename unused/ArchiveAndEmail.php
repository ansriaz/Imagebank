<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

use App\Models\Image;
use App\Models\ImageClasslabels;
use App\Models\Classlabel;

use App\Logic\Services\DBService;
use DB;
use Log;

class ArchiveAndEmail extends Command
{

    protected $search;
    protected $datasets;
    protected $dataset_title;
    protected $archive_file_name;
    protected $dbService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archive {search?} {datasets?} {dataset_title?} {name?} {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive and Email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( DBService $dbService )
    {
        parent::__construct();

        $this->dbService = $dbService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->search = $this->argument('search');
        $this->datasets = json_decode($this->argument('datasets'));
        $this->dataset_title = $this->argument('dataset_title');
        $this->archive_file_name = $this->argument('name');
        $this->user_id = $this->argument('user_id');

        Log::info("Archive started");
        Log::info($this->search);
        Log::info($this->datasets);
        Log::info($this->dataset_title);
        Log::info($this->archive_file_name);
        Log:info($this->user_id);

        $this->info("Archive started");

        $url = $this->createArchive();

        Log::info($url);
    }

    /**
     * Archives the array of images.
     *
     * @return
     */
    protected function createArchive( )
    {
        $images = [];

        $zip = new \ZipArchive;

        $zip_file = public_path() .'/archives/'. $this->archive_file_name;
        Log::info('Zip file destination: ' . $zip_file);
        if ($zip -> open($zip_file, \ZipArchive::CREATE) === TRUE)
        {
            foreach ($this->datasets as $value)
            {
                Log::info("value: ".$value);
                $folderName = $value;

                if($this->search === 1) {

                    $images = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                                -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                                -> where('classlabel.title', 'like', '%'.$value.'%')
                                -> orWhere('tags', 'like', '%'.$value.'%')
                                -> select('images.*')
                                -> get();
                } else {

                    $label = $this->dbService->getUserDatasetByName($folderName);

                    if(!is_null($label)) {

                        Log::info($this->user_id);
                        Log::info($label->id);
                        $images = Image::where('user_id',$this->user_id)
                              -> where('dataset_id',$label->id)
                              -> get();

                        Log::info($images);

                    } else {

                        $dataset_id = $this->dbService->getUserDatasetByName($this->dataset_title)->id;

                        $label = $this->dbService->getClassIdByTitleAndDatasetId($folderName, $dataset_id);
                        Log::info($label);
                        $images = DB::table('images')
                              -> join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                              -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                              -> where('images.user_id',$this->user_id)
                              -> where('imageclasslabels.class_id',$label->id)
                              -> select('images.*')
                              -> get();
                    }

                }

                Log::info($folderName.': '.count($images));

                // Log::info($images);

                if($zip->addEmptyDir($folderName))
                {
                    Log::info('Created a new root directory');
                }
                else
                {
                    Log::info('Could not create the directory');
                }

                $imagesData = array();

                foreach($images as $image)
                {
                    $file = public_path().$image->uri.$image->filename;
                    Log::info($image->id.' file to download: '. $file);
                    $zip->addFile($file, $folderName.'/'.$image->filename);

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
                $zip->addFromString( $folderName.'/'.$folderName.'.csv', stream_get_contents($file) );
                
                //close the file
                fclose($file);
            }

            $zip->close();

            return route('downloadArchive', [$zip_file]);
        }

    }
}
