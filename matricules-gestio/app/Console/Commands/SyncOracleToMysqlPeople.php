<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncOracleToMysqlPeople extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-oracle-to-mysql-people';

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
        include base_path('scripts/syncOracleToMysqlPeople.php');
    }
}
