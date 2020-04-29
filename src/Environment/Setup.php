<?php

namespace MorningTrain\Laravel\Dev\Commands\Environment;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the environment';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('config:clear');
        $this->call('env:copy-from-example');
        $this->call('key:generate');
    }

}
