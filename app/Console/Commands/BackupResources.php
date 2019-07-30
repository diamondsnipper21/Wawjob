<?php

namespace iJobDesk\Console\Commands;

use Illuminate\Console\Command;

use Config;

class BackupResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:resources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup uploaded files and database in daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $db_host = env('DB_DATABASE');
        $db_user = env('DB_USERNAME');
        $db_pass = env('DB_PASSWORD');

        exec("mysqldump -h $db_host -u $db_user -p $db_pass local.ijobdesk.com > uploads/1.sql");
    }
}
