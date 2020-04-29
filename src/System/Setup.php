<?php

namespace MorningTrain\Laravel\Dev\Commands\System;

use Illuminate\Console\Command;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:setup {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the project for the first time.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('force') || $this->confirm('WARNING: This will clear the DATABASE. Are you sure you want to continue?')) {

            $this->call('config:clear');

            $this->call('env:setup');
            $this->call('db:setup');

            $this->call('passport:keys');
            $this->call('config:cache');

            $this->call('storage:link');

            $this->call('system:build', $this->option('force')?['--force' => true]:[]);

            $this->info('The system was successfully set up');

        }
    }
}