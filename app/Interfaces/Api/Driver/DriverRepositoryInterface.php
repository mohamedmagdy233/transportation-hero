<?php

namespace App\Interfaces\Api\Driver;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface DriverRepositoryInterface{

    public function registerDriver(Request $request): JsonResponse;
    public function registerDriverDoc(Request $request): JsonResponse;
    public function checkDocument(Request $request): JsonResponse;
    public function changeStatus(Request $request): JsonResponse;
    public function updateDriverDetails(Request $request): JsonResponse;
    public function updateDriverDocument(Request $request): JsonResponse;
    public function startQuickTrip(Request $request): JsonResponse;
    public function endQuickTrip(Request $request): JsonResponse;
    public function acceptTrip(Request $request): JsonResponse;
    public function cancelTrip(Request $request): JsonResponse;
    public function startTrip(Request $request): JsonResponse;
    public function endTrip(Request $request): JsonResponse;
    public function driverAllTrip(Request $request): JsonResponse;
    public function driverWallet(): JsonResponse;
    public function getInfoDriver();
    public function getTripStatus(): JsonResponse;
    public function driverProfit(Request $request): JsonResponse;
    public function driverLocation(Request $request): JsonResponse;
    //Made By https://github.com/eldapour (eldapour)
}
