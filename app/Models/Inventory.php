<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $primaryKey = 'InventoryID';

    protected $fillable = [
        'ProductID',
        'Quantity',
        'UpdatedAt',
    ];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }
}
