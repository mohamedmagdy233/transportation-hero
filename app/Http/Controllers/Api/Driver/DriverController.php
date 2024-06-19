<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Interfaces\Api\Driver\DriverRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public DriverRepositoryInterface $driverRepository; // encapsulation the driverRepository

    public function __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    } // __construct

    public function registerDriver(Request $request): JsonResponse
    {
        return $this->driverRepository->registerDriver($request);
    } // registerDriver

    public function registerDriverDoc(Request $request): JsonResponse
    {
        return $this->driverRepository->registerDriverDoc($request);
    } // registerDriverDoc

    public function checkDocument(Request $request): JsonResponse
    {
        return $this->driverRepository->checkDocument($request);
    } // checkDocument

    public function changeStatus(Request $request): JsonResponse
    {
        return $this->driverRepository->changeStatus($request);
    } // changeStatus

    public function updateDriverDetails(Request $request): JsonResponse
    {
        return $this->driverRepository->updateDriverDetails($request);
    } // updateDriverDetails

    public function updateDriverDocument(Request $request): JsonResponse
    {
        return $this->driverRepository->updateDriverDocument($request);
    } // updateDriverDocumentss

    public function driverWallet(): JsonResponse
    {
        return $this->driverRepository->driverWallet();
    } // updateDriverDocument

    public function driverProfit(Request $request): JsonResponse
    {
        return $this->driverRepository->driverProfit($request);
    } // updateDriverDocument

    public function getInfoDriver(): JsonResponse
    {
        return $this->driverRepository->getInfoDriver();
    } // getInfoDriver
}
