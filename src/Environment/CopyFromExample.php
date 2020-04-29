<?php

namespace MorningTrain\Laravel\Dev\Commands\Environment;

use Illuminate\Console\Command;

class CopyFromExample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:copy-from-example {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assists making the .env file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $env_path = base_path('.env');
        $env_example_path = base_path('.env.example');

        if(!file_exists($env_example_path)) {
            throw new \Exception('Example .env file does not exist in project root (.env.example)');
        }

        if(!file_exists($env_path) || $this->shouldForceCopy()) {
            copy($env_example_path, $env_path);
        }
    }

    protected function shouldForceCopy()
    {
        return $this->option('force') || $this->confirm('WARNING: An .env file already exists, would you like to replace it anyways?');
    }

}
