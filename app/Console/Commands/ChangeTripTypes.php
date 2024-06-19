<?php

namespace App\Console\Commands;

use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Traits\FirebaseNotification;

class ChangeTripTypes extends Command
{

    use FirebaseNotification;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trip:changeTrip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Trip type to with or without when this trip Her time has come';

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
        $scheduledTrips = Trip::where('trip_type', 'scheduled')->get();
        
        $currentTime = Carbon::now();
        
        foreach ($scheduledTrips as $trip) {
            $scheduledTime = Carbon::parse($trip->created_at);

            if ($currentTime->gte($scheduledTime)) {
                $trip->update(['trip_type' => 'with']);
                $userData = [
                    'title' => 'رحلة مجدولة',
                    'body' => 'رحلتك في انتظارك الآن',
                    'trip_id' => $trip->id,
                ];
                $this->sendFirebaseNotification($userData, $trip->user_id, 'user');
                $data = [
                    'title' => 'رحلة جديدة',
                    'body' => 'هناك رحلة جديدة في الانتظار',
                    'trip_id' => $trip->id,
                ];
    
                $this->sendFirebaseNotification($data, $trip->user_id, 'nearDrivers');
            }
        }
        $this->info('Change trip types Changed successfully.');
    }
}
