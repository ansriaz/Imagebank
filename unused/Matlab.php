<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Logic\Services\CoreServices;

use Log;
use App\Models\File;
use App\Models\Image;

class Matlab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matlab {user_id} {project} {images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Matlab as app_main.mat is the main file. First argument is project name, 2nd argument is an array of search terms to run algorithm on it. For example: matlab 1 Project_Ans [history,pakistan]';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CoreServices $core)
    {
        parent::__construct();
        $this->core = $core;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user_id = $this->argument('user_id');
        $projectName = $this->argument('project');
        $images = $this->argument('images');
        $searchTerms = explode(",", $images);

        Log::info("[Matlab Controller]");
        Log::info($user_id);
        Log::info($projectName);
        Log::info($images);

        // $this->info("Archive started");
        $project = File::join('userfiles','files.id','=','userfiles.fileId')
                        -> where('userfiles.userId','=',$user_id)
                        -> where('files.filename','like','%'.$projectName.'%') -> first();

        Log::info('[PROJECT FOLDER]: '.$project);

        $images = $this->getImages($searchTerms);
        if(!is_null($images))
        {
            $outputDir  = $this->core->makeFolder(public_path().$project->filepath.'/'.'output');
            $outputFile = $outputDir.'/out.txt';
            $inputDir = public_path().$project->filepath;
            $command = "/Applications/MATLAB_R2016a.app/bin/matlab -nodisplay -nojvm -logfile ".$outputFile." -r \" cd ".$outputFile."; app_main; quit;\" 2>&1";
            // $command = "matlab -sd ".$inputDir." -r phpcreatefile('".$outputDir."\\".$filename."')";
            // $command = 'echo $PATH';
            Log::info('[COMMAND]: '.$command);
            $result = exec($command, $output, $return);
            if(!$return){
                Log::info('[exec command status]: '.$return);
            } else {
                Log::info('[exec command status]: doesn\'t work');
            }
            Log::info('[exec command output]: '.json_encode($output));
            file_put_contents($outputDir . '/exec_result.txt', $result);
        }

        // $this->getOutput();

        // Log::info($url);
    }

    /**
     * Archives the array of images.
     *
     * @return
     */
    function getImages ( $searchTerms )
    {
        if(!is_null($searchTerms))
        {
            $imagesForProject = array();
            foreach ($searchTerms as $value) {
                $query_param = preg_replace('/\s+/', '', $value);
                $images = Image::join('imageclasslabels', 'images.id','=','imageclasslabels.image_id')
                                -> join('classlabel', 'imageclasslabels.class_id','=','classlabel.id')
                                -> where('classlabel.title', 'like', '%'.$value.'%')
                                -> orWhere('tags', 'like', '%'.$value.'%')
                                -> select('images.*')
                                -> get();
                Log::info(count($images));
                foreach ($images as $image) {
                    array_push($imagesForProject, (array)$image);
                }
            }
            return $imagesForProject;
        }
        return false;
    }

    function getOutput ()
    {
        // Setup the file descriptors
        $descriptors = [
            0 => ['pipe', 'w'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        // Start the script
        $proc = proc_open($cmd, $descriptors, $pipes);

        // Read the stdin
        $stdin = stream_get_contents($pipes[0]);
        fclose($pipes[0]);

        // Read the stdout
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        // Read the stderr
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        // Close the script and get the return code
        $return_code = proc_close($proc);
    }
}
