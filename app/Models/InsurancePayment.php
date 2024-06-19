<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsurancePayment extends Model
{
    use HasFactory;

    protected $table = 'insurance_payments';

    protected $fillable = [
        'driver_id',
        'from',
        'to',
        'transaction_id',
        'type',
        'amount',
        'status',
    ];

    public function driver() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
