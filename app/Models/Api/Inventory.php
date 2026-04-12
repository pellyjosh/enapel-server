<?php

namespace App\Models\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'name', 
        'sku', 
        'category', 
        'description', 
        'quantity', 
        'price', 
        'staffid', 
        'batch_number', 
        'expiry_date',
        'cost_price',
        'unit_name',
        'units_per_pack',
        'pack_price_override',
        'parent_id',
        'variation_name',
        'packs_per_carton',
        'carton_price_override'
    ];

    protected $casts = [
        'id' => 'integer',
        'quantity' => 'integer',
        'price' => 'float',
        'cost_price' => 'float',
        'pack_price_override' => 'float',
        'units_per_pack' => 'integer',
        'packs_per_carton' => 'integer',
        'parent_id' => 'integer',
        'carton_price_override' => 'float',
    ];


    public function getPriceAttribute($value)
    {
        return number_format((float)$value, 2, '.', '');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'staffid', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Inventory::class, 'parent_id');
    }

    public function variations()
    {
        return $this->hasMany(Inventory::class, 'parent_id');
    }

    public function getProfitMarginAttribute()
    {
        if ($this->price <= 0) return 0;
        return (($this->price - $this->cost_price) / $this->price) * 100;
    }

    protected static function booted()
    {
        static::created(function ($inventory) {
            if (empty($inventory->sku)) {
                $inventory->sku = 'EP-' . str_pad($inventory->id, 6, '0', STR_PAD_LEFT);
                $inventory->saveQuietly();
            }
        });
    }
}
