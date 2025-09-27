<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $primaryKey = 'PurchaseItemID';

    protected $fillable = [
        'PurchaseID',
        'ProductID',
        'Quantity',
        'UnitPrice',
        'TotalPrice',
    ];

    protected $casts = [
        'UnitPrice' => 'decimal:2',
        'TotalPrice' => 'decimal:2',
    ];

    // Purchase item belongs to a purchase
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'PurchaseID');
    }

    // Purchase item belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }

    // Calculate total price based on quantity and unit price
    public function calculateTotalPrice()
    {
        return $this->Quantity * $this->UnitPrice;
    }
}
