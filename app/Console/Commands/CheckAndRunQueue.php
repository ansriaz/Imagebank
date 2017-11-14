<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckAndRunQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkqueue:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will check if queue listner is running or not. If it is not running then it will run it.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (file_exists(__DIR__ . '/queue.pid')) {
            $pid = file_get_contents(__DIR__ . '/queue.pid');
            $result = exec('ps | grep ' . $pid);
            if ($result == '') {
                $this->runCommand();
            }
        } else {
            $this->runCommand();
        }
    }

    function runCommand ()
    {
        $command = 'php artisan queue:listen > /dev/null & echo $!';
        $number = exec($command);
        file_put_contents(__DIR__ . '/queue.pid', $number);
    }

}
