<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_id',
        'account_date',
        'total_income',
        'total_expenses',
        'net_profit',
        'income_notes',
        'expense_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'account_date' => 'date',
    ];

    /**
     * Get the supplier that owns the daily account.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get formatted total income.
     */
    public function getFormattedTotalIncomeAttribute()
    {
        return '₹' . number_format($this->total_income, 2);
    }

    /**
     * Get formatted total expenses.
     */
    public function getFormattedTotalExpensesAttribute()
    {
        return '₹' . number_format($this->total_expenses, 2);
    }

    /**
     * Get formatted net profit.
     */
    public function getFormattedNetProfitAttribute()
    {
        return '₹' . number_format($this->net_profit, 2);
    }
}