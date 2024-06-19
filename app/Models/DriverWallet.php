<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'day',
        'total',
        'vat_total',
        'status'
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'driver_id', 'driver_id')
            ->where('type', '=', 'complete')
            ->where('created_at', '>=', Carbon::now()->subDays(7)) // Get trips from the last 7 days
            ->where('ended', '=', true)
            ->orderBy('created_at','desc');
    }
}
