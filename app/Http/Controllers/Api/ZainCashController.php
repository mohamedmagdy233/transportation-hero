<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\InitialPaymentRequest;
use App\Models\InsurancePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Waad\ZainCash\Facades\ZainCash;

class ZainCashController extends Controller
{
    /**
     * Create Request Transaction
     *
     * @param InitialPaymentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initialTransaction(InitialPaymentRequest $request)
    {
        $insurance_payment = InsurancePayment::first();
        $zainCashPayment = ZainCash::setAmount(1010)
            ->setServiceType('Book')
            ->setOrderId(Str::random(36))
            ->setIsTest(true)
            ->setIsReturnArray(true);

        $transactionId = $zainCashPayment->createTransaction();
        $transactionId = $transactionId['id'];

        $zainCashPayment = ZainCash::make()
            ->setTransactionID($transactionId)
            ->setIsReturnArray(true);

        // Check transaction details
        $checkTransaction = $zainCashPayment->checkTransaction();

        // Process transaction
        $processingTransaction = $zainCashPayment->processingTransaction("9647802999569", '1234');

        return response()->json([
            'checkTransaction' => $checkTransaction,
            'processingTransaction' => $processingTransaction,
        ]);
    }

    /**
     * Pay Transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payTransaction(Request $request)
    {
        // Validate request data
        $request->validate([
            'transaction_id' => 'required|string',
            'phone_number' => 'required|string',
            'pin' => 'required|string',
            'otp' => 'required|string',
        ]);

        $transactionId = $request->input('transaction_id');
        $phoneNumber = $request->input('phone_number');
        $pin = $request->input('pin');
        $otp = $request->input('otp');

        // Pay transaction
        $zainCashPayment = ZainCash::make()
            ->setTransactionID($transactionId)
            ->setIsReturnArray(true);

        $payDetails = $zainCashPayment->payTransaction($phoneNumber, $pin, $otp);

        return response()->json([
            'pay' => $payDetails,
        ]);
    }
}
