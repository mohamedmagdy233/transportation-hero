<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressFavorite extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'address',
        'lat',
        'long',
    ];
}
