<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $primaryKey = 'SupplierID';

    protected $fillable = [
        'Name',
        'Contact',
        'Address',
    ];

    // Supplier has many Products
    public function products()
    {
        return $this->hasMany(Product::class, 'SupplierID');
    }//
}
