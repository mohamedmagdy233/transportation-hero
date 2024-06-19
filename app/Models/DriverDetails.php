<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDetails extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function driver()
    {
        return $this->belongsTo(User::class,'driver_id','id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class,'area_id','id');
    }

}
