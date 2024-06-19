<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Interfaces\Api\Driver\DriverRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public DriverRepositoryInterface $driverRepository; // encapsulation the driverRepository

    public function __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    } // __construct

    public function startQuickTrip(Request $request): JsonResponse
    {
        return $this->driverRepository->startQuickTrip($request);
    } // startQuickTrip

    public function endQuickTrip(Request $request): JsonResponse
    {
        return $this->driverRepository->endQuickTrip($request);
    } // endQuickTrip

    public function acceptTrip(Request $request): JsonResponse
    {
        return $this->driverRepository->acceptTrip($request);
    } // endAcceptTrip

    public function cancelTrip(Request $request): JsonResponse
    {
        return $this->driverRepository->cancelTrip($request);
    } // cancelTrip
    public function startTrip(Request $request): JsonResponse
    {
        return $this->driverRepository->startTrip($request);
    } // start trip

    public function endTrip(Request $request): JsonResponse
    {
        return $this->driverRepository->endTrip($request);
    } // end trip

    public function driverAllTrip(Request $request): JsonResponse
    {
        return $this->driverRepository->driverAllTrip($request);
    } // all trip

    public function getTripStatus(): JsonResponse
    {
        return $this->driverRepository->getTripStatus();
    } // getTripStatus

    public function driverLocation(Request $request): JsonResponse
    {
        return $this->driverRepository->driverLocation($request);
    } // driver location

}
