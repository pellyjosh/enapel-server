<?php

namespace App\Models\Api;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function product()
    {
        return $this->belongsTo(Inventory::class, 'product_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
