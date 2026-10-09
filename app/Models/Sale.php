<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'receipt_number', 'customer_name', 'total'
    ];

    protected $casts = ['total' => 'decimal:2'];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}