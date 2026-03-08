<?php

namespace App\Models\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['name', 'quantity', 'price', 'staffid'];

    protected $casts = [
        'id' => 'integer',
        'quantity' => 'integer',
        'price' => 'float',
    ];


    public function getPriceAttribute($value)
    {
        return number_format((float)$value, 2, '.', '');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'staffid', 'id');
    }
}
