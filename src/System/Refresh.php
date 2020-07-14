<?php

namespace MorningTrain\Laravel\Dev\Commands\System;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use MorningTrain\Laravel\Dev\Commands\System\Events\SystemRefreshing;

class Refresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reloads config and updates permissions';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->call('cache:clear');
        $this->call('route:clear');

        App::environment('local') ?
            $this->call('config:clear') :
            $this->call('config:cache');

        Event::dispatch(new SystemRefreshing($this));

        $this->info('The system was successfully refreshed.');
    }
}
