<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use TitasGailius\Terminal\Terminal;

class SyncCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sync
                            {path : The relative path to sync (required)}
                            {direction=down : Sync direction. Up or down (required)}
                            {--r|recursive : Specify if the sync should be recursive or not (optional)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sync data from or to simple hosting';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $environment = $this->choice(
            'Which environment do you want to sync:',
            app('git')->getRemotes()->pluck('website', 'remote')->toArray(),
            'origin'
        );

        $remote = app('git')->getRemote($environment);
        $command = sprintf(
            "mkdir -p %s; sftp %s@%s:/vhosts/%s/%s <<< $'lpwd
            lcd %s
            %s %s *'",
            $this->argument('path'),
            $remote['user'],
            $remote['sftp'],
            $remote['website'],
            $this->argument('path'),
            $this->argument('path'),
            $this->argument('direction') == 'up' ? 'put' : 'get',
            $this->option('recursive') ? '-r' : '',
        );

        $message = sprintf(
            "%s %s",
            $this->argument('direction') == 'up' ? 'Uploading' : 'Downloading',
            $this->argument('path')
        );
        $result = $this->task($message, function () use ($command) {
            return Terminal::timeout(0)->run($command);
        }, 'working...');

        $message = $result ? "Synchronization succeed" : "Synchronization failed";
        $this->notify("Simple hosting", $message, resource_path("logo-gandi.png"));
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
