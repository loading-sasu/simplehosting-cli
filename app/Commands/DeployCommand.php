<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use TitasGailius\Terminal\Terminal;

class DeployCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'deploy';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Deploy current codebase on simple hosting';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $environment = $this->choice(
            'Which environment do you want to deploy:',
            app('git')->getRemotes()->pluck('website', 'remote')->toArray(),
            'origin'
        );

        $remote = app('git')->getRemote($environment);
        $command = sprintf(
            'ssh %s@%s deploy %s',
            $remote['user'],
            $remote['host'],
            $remote['repository'],
        );

        Terminal::timeout(0)->run($command);

        $result = $this->task("Deploying code", function () use ($command) {
            return Terminal::timeout(0)->run($command);
        }, 'working...');

        $message = $result ? "Deployment succeed" : "Deployment failed";
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
