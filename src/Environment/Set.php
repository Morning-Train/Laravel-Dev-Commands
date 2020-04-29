<?php

namespace MorningTrain\Laravel\Dev\Commands\Environment;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Set extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:set {--key=} {--value=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows setting values in the .env file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $envKey = strtoupper($this->option('key'));
        $envValue = $this->option('value');

        if (!$envKey) {
            throw new \Exception('Missing ENV key');
        }

        if (!$envValue) {
            $envValue = "";
        }

        if (Str::contains($envValue, ' ')) {
            $envValue = '"' . $envValue . '"';
        }

        $envFile = app()->environmentFilePath();

        if (!file_exists($envFile)) {
            throw new \Exception('Missing .env file');
        }

        $str = file_get_contents($envFile);

        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$envKey}=");
        $endOfLinePosition = strpos($str, "\n", $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

        // If key does not exist, add it
        if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
            $str .= "{$envKey}={$envValue}\n";
        } else {
            $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
        }

        $str = substr($str, 0, -1);

        if (!file_put_contents($envFile, $str)) return false;

        return true;

    }

}
