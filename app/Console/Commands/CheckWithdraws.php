<?php

namespace iJobDesk\Console\Commands;

use Illuminate\Console\Command;

use iJobDesk\Models\Cronjob;

class CheckWithdraws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckWithdraws:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check withdraw requests and send notification for the overdue requests.';

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
        Cronjob::crCheckWithdraws();
    }
}
