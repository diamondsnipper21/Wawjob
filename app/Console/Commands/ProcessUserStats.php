<?php

namespace iJobDesk\Console\Commands;

use Illuminate\Console\Command;

use iJobDesk\Models\Cronjob;

class ProcessUserStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessUserStats:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process user stats. Run monthly.';

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
        Cronjob::crProcessUserStat();
    }
}
