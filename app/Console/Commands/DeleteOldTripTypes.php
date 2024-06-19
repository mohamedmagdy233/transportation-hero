<?php

namespace App\Console\Commands;

use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldTripTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trip:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete trip types older than 8 hours';

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
     * @return int
     */
    public function handle()
    {
        $eightHoursAgo = Carbon::now()->subHours(8);
        Trip::where('created_at', '<', $eightHoursAgo)->where('type', 'new')->delete();
        $this->info('Old trip types deleted successfully.');
    }
}
