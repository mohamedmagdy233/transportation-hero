<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverDocuments extends Model
{
    use HasFactory;

    protected $table = 'driver_documents';

    protected $fillable = [
        'agency_number',
        'bike_license',
        'id_card',
        'house_card',
        'bike_image',
        'status',
        'driver_id',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(DriverDetails::class,'driver_id','driver_id');
    }

    public function drivers(): BelongsTo
    {
        return $this->belongsTo(User::class,'driver_id','id');
    }
}
