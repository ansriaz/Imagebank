<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Logic\Services\TwitterService\TwitterCrawler;

use Log;

class CrawlTweets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:crawltweets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl tweets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TwitterCrawler $twitter)
    {
        parent::__construct();

        $this->twitter = $twitter;
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

        $tweets = $this->twitter->crawl($tags);

        $this->info('Todays job is done');
    }
}
