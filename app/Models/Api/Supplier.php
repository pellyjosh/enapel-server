<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{

    protected $fillable = ['supplier', 'company', 'phone', 'email', 'address'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }


}
