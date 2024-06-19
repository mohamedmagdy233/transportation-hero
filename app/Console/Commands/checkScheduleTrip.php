<?php

namespace App\Console\Commands;

use App\Models\Trip;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Traits\FirebaseNotification;

class checkScheduleTrip extends Command
{
    use FirebaseNotification;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check scheduled trips and send notifications if remaining time is 4 hours';

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
        $trips = Trip::where('trip_type', 'scheduled')->where('created_at', '<=', Carbon::now()->addHours(4))
            ->get();
            
            foreach ($trips as $trip) {
                $user_id = $trip->user_id;
            $data = [
                'title' => 'رحلة مجدولة',
                'body' => 'رحلتك المجدولة على بعد اربع ساعات من الان',
                'trip_id' => $trip->id
            ];
            $this->sendFirebaseNotification($data,$user_id, 'acceptTrip', true);
        }
        $this->info('Scheduled trips checked successfully.');
        return 0;
    }
}
