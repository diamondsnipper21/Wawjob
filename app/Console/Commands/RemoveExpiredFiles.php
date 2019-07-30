<?php

namespace iJobDesk\Console\Commands;

use Illuminate\Console\Command;
use iJobDesk\Models\File;

class RemoveExpiredFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RemoveExpiredFiles:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired files';

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
        $files = File::where('target_id', 0)
                     ->whereNull('is_approved')
                     ->where('created_at', '<', date('Y-m-d H:i:s', strtotime('-1 hour')))
                     ->get();

        foreach ($files as $file) {
            $file->delete();
        }
    }
}
