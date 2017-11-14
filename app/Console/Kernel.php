<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\FlickrCrawlerCronJob::class,
        Commands\GoogleCrawl::class
    ];
    // Commands\ArchiveAndEmail::class,
    // Commands\CrawlTweets::class,
    // Commands\Matlab::class

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // crawl data weekly on saturdays at 01:00 AM
        $schedule->command('crawlflickr')
                 ->weekly()->saturdays()->at('01:00');

        // check and run queue listners if it it not running
        $schedule->command('checkqueue:listen')
                 ->everyThirtyMinutes();
    }
}
