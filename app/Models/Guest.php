<?php

namespace App\Models;

use App\Models\Api\Receipt;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $guarded = [];
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
