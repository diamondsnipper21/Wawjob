<?php

namespace iJobDesk\Console\Commands;

use Illuminate\Console\Command;

use iJobDesk\Models\Cronjob;

class ProcessAffiliateTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessAffiliateTransactions:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process affiliate transactions and send notificaction to admin for overdue requests. Run every day.';

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
        Cronjob::crCheckAffiliateTransactions();
    }
}
