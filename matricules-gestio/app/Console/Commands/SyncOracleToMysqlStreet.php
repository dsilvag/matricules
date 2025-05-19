<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncOracleToMysqlStreet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-oracle-to-mysql-street';

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
        include base_path('scripts/syncOracleToMysqlStreet.php');
    }
}
