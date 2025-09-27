<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'OrderID',
        'payment_method',
        'payment_status',
        'amount',
        'transaction_id',
        'payment_details',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID');
    }

    // Payment status constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    // Payment method constants
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_DEBIT_CARD = 'debit_card';
    const METHOD_PAYPAL = 'paypal';
    const METHOD_CASH_ON_DELIVERY = 'cash_on_delivery';
    const METHOD_BANK_TRANSFER = 'bank_transfer';

    public static function getPaymentMethods()
    {
        return [
            self::METHOD_CASH_ON_DELIVERY => 'Cash on Delivery',
            self::METHOD_CREDIT_CARD => 'Credit Card',
            self::METHOD_DEBIT_CARD => 'Debit Card',
            self::METHOD_PAYPAL => 'PayPal',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer'
        ];
    }

    public function isPending()
    {
        return $this->payment_status === self::STATUS_PENDING;
    }

    public function isCompleted()
    {
        return $this->payment_status === self::STATUS_COMPLETED;
    }

    public function markAsCompleted($transactionId = null)
    {
        $this->update([
            'payment_status' => self::STATUS_COMPLETED,
            'paid_at' => now(),
            'transaction_id' => $transactionId
        ]);
    }
}
