<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Logic\Services\CoreServices;
use App\Logic\Services\EmailService;
use App\Models\File;
use App\Models\Image;

use Log;

class MatlabJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    // run this from command line to before starting the job.
    // php artisan queue:listen --timeout=0

    protected $user_id;
    protected $projectName;
    protected $searchTerms;
    protected $core;
    protected $EmailService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $project)
    {
        $this->user_id = $user_id;
        $this->projectName = $project;
        // $this->searchTerms = explode(",", $images);

        Log::info("[Matlab Controller]");
        Log::info($this->user_id);
        Log::info($this->projectName);
        // Log::info($images);

        $this->core = new CoreServices;
        $this->EmailService = new EmailService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $project = File::join('userfiles','files.id','=','userfiles.fileId')
                        -> where('userfiles.userId','=',$this->user_id)
                        -> where('files.filename','like','%'.$this->projectName.'%') -> first();

        // Log::info('[PROJECT FOLDER]: '.$project);

        // $images = $this->getImages($this->searchTerms);
        // if(!is_null($images))
        // {
            $outputDir  = $this->core->makeFolder(public_path().$project->filepath.'/'.'output');
            $outputFile = $outputDir.'/out.txt';
            $inputDir = public_path().$project->filepath;

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // echo 'This is a server using Windows!';
                $command = "matlab -nodisplay -nojvm -logfile ".$outputFile." -r \" cd ".$inputDir."; app_main; quit;\" 2>&1";
            } else {
                // echo 'This is a server not using Windows!';
                $command = "/Applications/MATLAB_R2016a.app/bin/matlab -nodisplay -nojvm -logfile ".$outputFile." -r \" cd ".$inputDir."; app_main; quit;\" 2>&1";
            }
            // $command = "matlab -sd ".$inputDir." -r phpcreatefile('".$outputDir."\\".$filename."')";
            // $command = 'echo $PATH';
            // Log::info('[COMMAND]: '.$command);
            $result = exec($command, $output, $return);
            // if(!$return){
            //     Log::info('[exec command status]: '.$return);
            // } else {
            //     Log::info('[exec command status]: doesn\'t work');
            // }
            // Log::info('[exec command output]: '.json_encode($output));
            // file_put_contents($outputDir . '/exec_result.txt', $result);

            if(isset($this->user_id))
            {
                // Log::info('[In EMAIL]: '.$this->user_id);
                $job = $this->EmailService->SendEmailWithMatlabOutput($this->user_id, $outputFile);
            }
        // }

        // $this->getOutput();
    }

    /**
     * Archives the array of images.
     *
     * @return
     */
    // Currently this function is not being used
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
                // Log::info(count($images));
                foreach ($images as $image) {
                    array_push($imagesForProject, (array)$image);
                }
            }
            return $imagesForProject;
        }
        return false;
    }

    // To Get the output from command line
    // Currently this function is not working
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
