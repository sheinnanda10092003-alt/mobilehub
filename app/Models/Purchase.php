<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $primaryKey = 'PurchaseID';

    protected $fillable = [
        'SupplierID',
        'TotalAmount',
        'Status',
        'PurchaseDate',
        'Notes',
        'CreatedBy',
    ];

    protected $casts = [
        'PurchaseDate' => 'date',
        'TotalAmount' => 'decimal:2',
    ];

    // Purchase belongs to a supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID');
    }

    // Purchase belongs to a staff member (creator)
    public function creator()
    {
        return $this->belongsTo(Staff::class, 'CreatedBy');
    }

    // Purchase has many items
    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'PurchaseID');
    }

    // Calculate total amount from items
    public function calculateTotalAmount()
    {
        return $this->items()->sum('TotalPrice');
    }

    // Check if purchase is pending
    public function isPending()
    {
        return $this->Status === 'pending';
    }

    // Check if purchase is received
    public function isReceived()
    {
        return $this->Status === 'received';
    }

    // Check if purchase is cancelled
    public function isCancelled()
    {
        return $this->Status === 'cancelled';
    }
}
