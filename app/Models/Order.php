<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'OrderID';

    protected $fillable = [
        'CustomerID',
        'shipping_name',
        'shipping_email', 
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'notes',
        'OrderDate',
        'TotalAmount',
        'Status',
    ];

    protected $casts = [
        'OrderDate' => 'datetime',
        'TotalAmount' => 'decimal:2',
    ];

    protected $attributes = [
        'shipping_name' => '',
        'shipping_email' => '',
        'shipping_phone' => '',
        'shipping_address' => '',
        'shipping_city' => '',
        'shipping_state' => '',
        'shipping_zip' => '',
        'shipping_country' => 'Philippines',
        'notes' => '',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'OrderID');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'OrderID');
    }

    // Order status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public static function getOrderStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled'
        ];
    }

    public function getFullShippingAddressAttribute()
    {
        $address = $this->attributes['shipping_address'] ?? '';
        $city = $this->shipping_city ?? '';
        $state = $this->shipping_state ?? '';
        $zip = $this->shipping_zip ?? '';
        $country = $this->shipping_country ?? 'Philippines';
        
        $parts = array_filter([$address, $city, $state . ' ' . $zip, $country]);
        return implode(', ', $parts);
    }

    public function isPaid()
    {
        return $this->payment && $this->payment->isCompleted();
    }

    public function getFormattedOrderDateAttribute()
    {
        try {
            return $this->OrderDate instanceof \Carbon\Carbon 
                ? $this->OrderDate->format('M d, Y g:i A') 
                : $this->OrderDate;
        } catch (\Exception $e) {
            return $this->OrderDate;
        }
    }
}
