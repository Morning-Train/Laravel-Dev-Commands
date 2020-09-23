<?php

namespace MorningTrain\Laravel\Dev\Commands\Database;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Drop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops all database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = config('database.default');
        $database = config("database.connections.$connection.database");

        // Get tables
        $tables = DB::table('information_schema.tables')
            ->selectRaw('TABLE_NAME')
            ->where('TABLE_SCHEMA', $database)
            ->get();

        // Disable foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // Drop tables
        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            DB::statement("DROP TABLE IF EXISTS $database.$tableName");
        }

        // Enable foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->info('Database was dropped.');
    }
}
