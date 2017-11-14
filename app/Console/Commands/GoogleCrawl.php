<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Logic\Services\GoogleServices\GoogleTranslate;
use App\Logic\Services\GoogleServices\GoogleImages;

use Log;

class GoogleCrawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:googlecrawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl Google services';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GoogleTranslate $translator, GoogleImages $images)
    {
        parent::__construct();
        $this->translator = $translator;
        $this->images = $images;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tags = [];
        $file = fopen(resource_path() . "/files/events.txt", "r");
        while(!feof($file)){
            $line = fgets($file);
            $line = str_replace(array("\r\n", "\r", "\n", "\t"), '', $line);
            array_push($tags, $line);
        }
        fclose($file);

        Log::info(json_encode($tags));

        $videos = $this->translator->crawl($tags);
        // $videos = $this->images->crawl($tags);

        $this->info('Todays job is done');
    }
}
