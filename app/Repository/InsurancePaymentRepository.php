<?php

namespace App\Repository;

use App\Interfaces\InsurancePaymentInterface;
use App\Models\InsurancePayment;
use Yajra\DataTables\DataTables;

class InsurancePaymentRepository implements InsurancePaymentInterface
{

    public function index($request)
    {
        if ($request->ajax()) {
            $insurance_payments = InsurancePayment::query()->latest()->get();
            return DataTables::of($insurance_payments)
            ->editColumn('driver_id', function($insurance_payments) {
                return $insurance_payments->driver->name;
            })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.insurance_payments.index');
        }
    }
}
