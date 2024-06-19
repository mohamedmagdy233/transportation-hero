<?php

namespace App\Interfaces;

Interface TripInterface {

    public function complete($request);
    
    public function showCompleteTrip($trip);

    public function new($request);

    public function showNewTrip($trip);

    public function reject($request);

    public function showRejectTrip($trip);

    public function delete($request);
}
