<?php

namespace iJobDesk\Console\Commands;

use Illuminate\Console\Command;

use iJobDesk\Models\Cronjob;

class JobRecommendation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'JobRecommendation:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an emaiil about job recommendation to freelancers';

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
        Cronjob::crJobRecommendation();
    }
}
