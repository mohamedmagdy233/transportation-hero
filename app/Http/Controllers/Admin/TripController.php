<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Interfaces\TripInterface;
use App\Http\Controllers\Controller;

class TripController extends Controller
{
    protected TripInterface $tripInterface;

    public function __construct(TripInterface $tripInterface)
    {
        $this->tripInterface = $tripInterface;
    }

    public function complete(Request $request)
    {
        return $this->tripInterface->complete($request);
    }

    public function showCompleteTrip($trip)
    {
        return $this->tripInterface->showCompleteTrip($trip);
    }

    public function new(Request $request)
    {
        return $this->tripInterface->new($request);
    }

    public function showNewTrip($trip)
    {
        return $this->tripInterface->showNewTrip($trip);
    }

    public function reject(Request $request)
    {
        return $this->tripInterface->reject($request);
    }

    public function showRejectTrip($trip)
    {
        return $this->tripInterface->showRejectTrip($trip);
    }

    public function delete(Request $request)
    {
        return $this->tripInterface->delete($request);
    }
}
