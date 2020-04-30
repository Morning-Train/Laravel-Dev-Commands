<?php

namespace MorningTrain\Laravel\Dev\Commands\Database;

use Illuminate\Console\Command;
use DB;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:setup';

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
        $this->testConnection();
    }

    protected function setEnv($envKey, $envValue)
    {

        $envFile = app()->environmentFilePath();
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

    protected function handleEmptySchemaName()
    {
        $default_database_connection = config('database.default');

        $this->info('No database name was provided.');

        $schemaName = $this->ask('What is the name of the database?');

        $this->setEnv('DB_DATABASE', $schemaName);

        config(["database.connections.$default_database_connection.database" => $schemaName]);

        DB::purge();
    }

    protected function handleHostname()
    {
        $default_database_connection = config('database.default');
        $new_host = $this->ask('What is the correct host?');
        $this->setEnv('DB_HOST', $new_host);
        config(["database.connections.$default_database_connection.host" => $new_host]);
        $this->info("The database host has been set to $new_host");
        DB::purge();
    }

    protected function handleUsername()
    {
        $default_database_connection = config('database.default');
        $default_username = config("database.connections.$default_database_connection.username");
        $username = $this->ask('What is your database username?', $default_username);
        $this->setEnv('DB_USERNAME', $username);
        config(["database.connections.$default_database_connection.username" => $username]);
        DB::purge();
    }

    protected function handlePassword()
    {
        $default_database_connection = config('database.default');
        $default_password = config("database.connections.$default_database_connection.password");
        $password = $this->ask('What is your database password?', $default_password);
        $this->setEnv('DB_PASSWORD', $password);
        config(["database.connections.$default_database_connection.password" => $password]);
        DB::purge();
    }

    protected function handleCreateDatabase($schemaName)
    {
        $default_database_connection = config('database.default');

        $charset = config("database.connections.$default_database_connection.charset", 'utf8mb4');
        $collation = config("database.connections.$default_database_connection.collation", 'utf8mb4_unicode_ci');

        DB::purge();

        config(["database.connections.$default_database_connection.database" => null]);

        $query = "CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;";

        DB::statement($query);
        config(["database.connections.$default_database_connection.database" => $schemaName]);

        DB::purge();

        $this->info('Database has been created!');
    }

    protected function testConnection()
    {


        $default_database_connection = config('database.default');

        $schemaName = config("database.connections.$default_database_connection.database");

        if (empty($schemaName)) {
            $this->handleEmptySchemaName();
        }

        try {
            DB::connection()->getPdo();
        } catch (\PDOException $e) {

            switch ($e->getCode()) {
                case 1045: // Access denied for user

                    $this->info('Access was denied for the current user');

                    $this->handleUsername();
                    $this->handlePassword();

                    break;
                case 1046: // No database selected
                case 1049: // Unknown database

                    $schemaName = config("database.connections.$default_database_connection.database");

                    if (empty($schemaName)) {
                        $this->handleEmptySchemaName();
                    } else {

                        $this->info('Unknown database: ' . $schemaName);

                        if (!$this->confirm('Would you like to create it?', true)) {
                            $schemaName = $this->ask('Then what is the name of the database?');
                        }

                        $this->call('db:create', ['--name' => $schemaName]);

                    }

                    break;
                case 2002: // Connection refused
                    $host = config("database.connections.$default_database_connection.host");
                    $this->info("The connection was refused when trying to connect to $host");
                    $this->handleHostname();
                    break;
                default:
                    $this->info('Unknown database error code: ' . $e->getCode());
                    $this->info($e->getMessage());

                    throw new \Exception('We were unable to resolve all database issues');
            }

            $this->testConnection();
        }

    }

}
