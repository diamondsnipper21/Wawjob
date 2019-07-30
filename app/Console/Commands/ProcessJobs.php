<?php

namespace iJobDesk\Console\Commands;

use Illuminate\Console\Command;

use iJobDesk\Models\Cronjob;

class ProcessJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessJobs:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process jobs. Run 1st day every month.';

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
        Cronjob::crProcessProjects();
    }
}
