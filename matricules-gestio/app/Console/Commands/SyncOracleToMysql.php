<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncOracleToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:oracle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        include base_path('scripts/syncOracleToMysqlDwelling.php');
        include base_path('scripts/syncOracleToMysqlPeople.php');
        include base_path('scripts/syncOracleToMysqlStreet.php');
        include base_path('scripts/syncOracleToMysqlTeleco.php');
    }
}
