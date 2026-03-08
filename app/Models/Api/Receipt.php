<?php

namespace App\Models\Api;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $guarded = [];

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }


    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

}
