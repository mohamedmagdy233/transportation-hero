<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class InitialPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "phone_number" => ["required", "string", "max:512"],
            "pin" => ["required", "string", "max:4", "min:4"],
        ];
    }
}

