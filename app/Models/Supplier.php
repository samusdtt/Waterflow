<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'gst_number',
        'pan_number',
        'subscription_status',
        'subscription_start_date',
        'subscription_end_date',
        'monthly_fee',
        'service_areas',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'service_areas' => 'array',
    ];

    /**
     * Get the users for the supplier.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the products for the supplier.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the orders for the supplier.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the payments for the supplier.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the jar records for the supplier.
     */
    public function jarRecords()
    {
        return $this->hasMany(JarRecord::class);
    }

    /**
     * Get the daily accounts for the supplier.
     */
    public function dailyAccounts()
    {
        return $this->hasMany(DailyAccount::class);
    }

    /**
     * Get the notifications for the supplier.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isSubscriptionActive()
    {
        return $this->subscription_status === 'active' && 
               $this->subscription_end_date > now();
    }

    /**
     * Check if subscription is expiring soon (within 7 days).
     */
    public function isSubscriptionExpiringSoon()
    {
        return $this->subscription_status === 'active' && 
               $this->subscription_end_date <= now()->addDays(7);
    }
}