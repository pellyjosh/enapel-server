<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['supplier_id', 'product', 'quantity', 'amount'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
