<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'receipt_number',
        'customer_name',
        'customer_document_type',
        'customer_document_number',
        'customer_address',
        'customer_email',
        'voucher_type',
        'subtotal',
        'discount_amount',
        'total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
