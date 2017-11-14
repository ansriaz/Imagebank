<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Logic\Services\CoreServices;
use App\Models\File;
use App\Models\Image;

use Log;

class UploadDataset extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    // run this from command line to before starting the job.
    // php artisan queue:listen --timeout=0

    protected $user_id;
    protected $dataset;
    protected $core;
    protected $target_dir;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $dataset, $dir)
    {
        $this->user_id = $user_id;
        $this->dataset = $dataset;
        $this->target_dir = $dir;

        Log::info("[Dataset Upload Controller]");
        Log::info($this->user_id);
        Log::info($this->dataset);

        $this->core = new CoreServices;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(isset($this->dataset))
        {
            $target_zip_name = $this->target_dir.'/'.$this->dataset;
            $dataset_zip_name = explode('.', $this->dataset);

            $target_extract_dir = $this->target_dir.'/'.$dataset_zip_name[0];
            $zip = new \ZipArchive;
            if ($zip -> open($target_zip_name) === TRUE)
            {
                $zip->extractTo($target_extract_dir);
                $zip->close();
                Log::info('[DATASET_ZIP_FILE]: done');
                unlink($target_zip_name);
                $this->uploadDatasetToDatabase($target_extract_dir);
                Log::info("Your .zip file was uploaded and unpacked. ".$target_zip_name);
            }
        } else {
            Log::info("There was a problem with the upload. Please try again.");
        }
    }

    function uploadDatasetToDatabase( $dir )
    {
        $datasets = $this->dirToArray($dir);
        $datasets = array_diff(scandir($dir), array('..', '.'));
        foreach ($datasets as $dset)
        {
            $dd = $dir . DIRECTORY_SEPARATOR . $dset;
            if (is_dir($dd))
            {
                Log::info('[FILE]: '.$dd);
                $file = glob($dd . DIRECTORY_SEPARATOR . '*.csv');
                if(isset($file))
                {
                    Log::info('[CSV FILE]: '.json_encode($file));
                    $csv = $this->getCSVasArray($file[0]);
                    Log::info('[FILE CONTENTS]: '.$csv);
                }
            }
        }
    }

    function dirToArray( $dir )
    {
        $result = array();

        $datasets = array_diff(scandir($dir), array('..', '.'));
        foreach ($datasets as $set)
        {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $set))
            {
               $result[$set] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $set);
            } else {
               $result[] = $set;
            }
        }
        return $result;
    }

    function getCSVasArray($file)
    {
        $title_row = null;
        $row = 1;
        $handle = fopen($file,"r");
        while (($data = fgetcsv($handle, ",")) !== FALSE)
        {
            $num = count($data);
            if($row == 1)
            {
                $title_row = $data;
                Log::info('[CVS LINE]: ' . json_encode( $data ));
                // array_push($title_row, $data[$c]);
            }
            $row++;
            // for ($c=0; $c < $num; $c++)
            // {
                Log::info('[CVS LINE]: '. $data );
            // }
        }
        fclose($handle);
    }
}
