<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Logic\Services\FlickrService\FlickrCrawler;

class FlickrCrawlerCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawlflickr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawling images from Flickr';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FlickrCrawler $fc)
    {
        $this->fc = $fc;
        parent::__construct();
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
            array_push($tags, $line);
        }
        fclose($file);

        // Log::info($tags);

        $photos = $this->fc->crawl($tags);

        // $videos = $this->youtube->crawl($tags);

        $this->info('Todays job is done');
    }
}
