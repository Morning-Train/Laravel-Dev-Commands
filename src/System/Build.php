<?php

namespace MorningTrain\Laravel\Dev\Commands\System;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use MorningTrain\Laravel\Dev\Commands\System\Events\SystemBuilding;
use MorningTrain\Laravel\Dev\Commands\System\Events\SystemStartsBuilding;
use MorningTrain\Laravel\Dev\Commands\System\Events\SystemStopsBuilding;

class Build extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:build {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reloads config and runs migrations';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('force') || $this->confirm('WARNING: This will clear the DATABASE. Are you sure you want to continue?')) {

            $this->call('down');

            $this->call('cache:clear');
            $this->call('route:clear');
            $this->call('config:clear');

            $this->call('db:setup');

            Event::dispatch(new SystemStartsBuilding($this));

            App::environment('local') ?
                $this->call('config:clear') :
                $this->call('config:cache');

            if(config('dev-commands.system.build.reset_migrations', true)) {
                $this->call('migrate:reset');
            }

            $this->call('migrate');

            Event::dispatch(new SystemBuilding($this));


            if(config('dev-commands.system.build.seed_database', true)) {
                $this->call('db:seed');
            }

            Event::dispatch(new SystemStopsBuilding($this));

            $this->call('up');

            $this->info('The system was successfully rebuilt.');
        }
    }
}
