<?php

namespace MorningTrain\Laravel\Dev\Commands\Database;

use Illuminate\Console\Command;
use DB;

class Create extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {--name=}';

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
        $default_database_connection = config('database.default');

        $schemaName = $this->option('name');

        if(!$schemaName) {
            $schemaName = config("database.connections.$default_database_connection.database");
        }

        if(!$schemaName) {
            $schemaName = $this->ask('What is the name of the database?');
        }

        $charset = config("database.connections.$default_database_connection.charset", 'utf8mb4');
        $collation = config("database.connections.$default_database_connection.collation", 'utf8mb4_unicode_ci');

        DB::purge();

        config(["database.connections.$default_database_connection.database" => null]);

        $query = "CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;";

        DB::statement($query);
        config(["database.connections.$default_database_connection.database" => $schemaName]);

        DB::purge();

        $this->info("Database has been created and saved as $schemaName");

        $this->call('env:set', ['--key' => 'DB_DATABASE', '--value' => $schemaName]);

    }

}
