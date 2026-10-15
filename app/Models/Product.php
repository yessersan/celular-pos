<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name', 'image', 'purchase_price', 'sale_price', 'stock'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function saleItems()
    {
        return $this->morphMany(SaleItem::class, 'saleable');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        $path = ltrim($this->image, '/');
        $path = preg_replace('#^(public/|storage/)#', '', $path);

        return Storage::disk('public')->url($path);
    }
}