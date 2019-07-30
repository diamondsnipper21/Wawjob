<?php

namespace iJobDesk\Console\Commands;

use Illuminate\Console\Command;

use iJobDesk\Models\Cronjob;

class ProcessDeposits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessDeposits:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process deposit requests and send notificaction to admin for overdue requests';

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
        Cronjob::crProcessDeposits(true); // CHANGED BY KCG, I have changed the "forcely" parameter
    }
}
