<?php

namespace MorningTrain\Laravel\Dev\Commands\System;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

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

            //
            // Config
            //

            $this->call('cache:clear');
            $this->call('route:clear');
            $this->call('permission:cache-reset');

            App::environment('local') ?
                $this->call('config:clear') :
                $this->call('config:cache');

            //
            // Migrations
            //

            $this->call('migrate:reset');
            $this->call('migrate');

            //
            // Permissions
            //

            $this->call('mt:refresh-permissions');

            //
            // Seed
            //

            $this->call('db:seed');

            $this->call('up');

            $this->info('The system was successfully rebuilt.');
        }
    }
}