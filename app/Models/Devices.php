<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devices extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sales()
    {
        return $this->hasMany(\App\Models\Api\Sales::class, 'device_id');
    }

    public function receipts()
    {
        return $this->hasMany(\App\Models\Api\Receipt::class, 'device_id');
    }
}
