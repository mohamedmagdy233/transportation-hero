<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\InsurancePaymentInterface;
use Illuminate\Http\Request;

class InsurancePaymentController extends Controller
{
    protected InsurancePaymentInterface $insurancePaymentInterface;

    public function __construct(InsurancePaymentInterface $insurancePaymentInterface)
    {
        $this->insurancePaymentInterface = $insurancePaymentInterface;
    }

    public function index(Request $request)
    {
        return $this->insurancePaymentInterface->index($request);
    }
}
