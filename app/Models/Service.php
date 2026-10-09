<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'price'];

    protected $casts = ['price' => 'decimal:2'];

    public function saleItems()
    {
        return $this->morphMany(SaleItem::class, 'saleable');
    }
}