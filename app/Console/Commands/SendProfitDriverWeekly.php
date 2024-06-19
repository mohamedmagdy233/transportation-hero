<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\DriverWallet;
use Illuminate\Console\Command;
use App\Traits\FirebaseNotification;

class SendProfitDriverWeekly extends Command
{
    use FirebaseNotification;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:profits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate weekly profits for drivers';

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
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $completedDrivers = DriverWallet::select('driver_id')
            ->whereBetween('day', [$startOfWeek, $endOfWeek])
            ->groupBy('driver_id')
            ->get();

        foreach ($completedDrivers as $driver) {
            $totalProfit = DriverWallet::where('driver_id', $driver->driver_id)
                ->whereBetween('day', [$startOfWeek, $endOfWeek])
                ->sum('total');

            $data = [
                'title' => 'الارباح الاسبوعية',
                'body' => 'تقرير بالارباح الاسبوعية',
            ];

            $driverId = User::find($driver->driver_id);
            $this->sendFirebaseNotification($data, $driverId->id, 'user');
        }

        $this->info('Weekly profits calculated and notifications sent successfully.');
    }
}
