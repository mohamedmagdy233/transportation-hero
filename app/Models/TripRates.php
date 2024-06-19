<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripRates extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'from',
        'to',
        'rate',
        'description',
    ];
}
