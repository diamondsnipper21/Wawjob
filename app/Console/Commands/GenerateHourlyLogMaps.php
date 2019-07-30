<?php

namespace iJobDesk\Console\Commands;

use Illuminate\Console\Command;

use iJobDesk\Models\Cronjob;

class GenerateHourlyLogMaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GenerateHourlyLogMaps:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate hourly log maps from logged times with tool. Run every hour.';

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
        Cronjob::crHourlyLogMap();
    }
}
