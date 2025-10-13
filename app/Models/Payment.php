<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'order_id',
        'supplier_id',
        'user_id',
        'type',
        'method',
        'status',
        'amount',
        'gateway',
        'gateway_transaction_id',
        'gateway_response',
        'notes',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the order that owns the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the supplier that owns the payment.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate payment ID.
     */
    public static function generatePaymentId()
    {
        return 'PAY_' . strtoupper(uniqid());
    }

    /**
     * Check if payment is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is failed.
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->amount, 2);
    }
}