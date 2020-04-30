<?php

namespace MorningTrain\Laravel\Dev\Commands\System;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use MorningTrain\Laravel\Dev\Commands\System\Events\SystemSettingUp;

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
            $this->call('storage:link');

            if(class_exists(\Laravel\Passport\Passport::class)) {
                $this->call('passport:keys');
            }

            Event::dispatch(new SystemSettingUp());

            $this->call('config:cache');

            $this->call('system:build', $this->option('force')?['--force' => true]:[]);

            $this->info('The system was successfully set up');

        }
    }
}
