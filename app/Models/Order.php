<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'supplier_id',
        'client_id',
        'staff_id',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'credit_points_used',
        'delivery_address',
        'notes',
        'delivery_date',
        'delivery_time',
        'delivered_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'delivery_date' => 'date',
        'delivery_time' => 'datetime:H:i',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the supplier that owns the order.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the client that owns the order.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the staff assigned to the order.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments for the order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Generate order number.
     */
    public static function generateOrderNumber()
    {
        $prefix = 'WM';
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastOrder ? (intval(substr($lastOrder->order_number, -4)) + 1) : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if order is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if order is delivered.
     */
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if order is paid.
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if order has due amount.
     */
    public function hasDueAmount()
    {
        return $this->due_amount > 0;
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAttribute()
    {
        return '₹' . number_format($this->total_amount, 2);
    }
}