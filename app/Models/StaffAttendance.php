<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'staff_id',
        'supplier_id',
        'attendance_date',
        'login_time',
        'logout_time',
        'total_hours',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attendance_date' => 'date',
        'login_time' => 'datetime:H:i',
        'logout_time' => 'datetime:H:i',
    ];

    /**
     * Get the staff that owns the attendance record.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the supplier that owns the attendance record.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Check if staff is currently logged in.
     */
    public function isLoggedIn()
    {
        return !is_null($this->login_time) && is_null($this->logout_time);
    }

    /**
     * Calculate total hours worked.
     */
    public function calculateTotalHours()
    {
        if ($this->login_time && $this->logout_time) {
            $login = \Carbon\Carbon::parse($this->attendance_date . ' ' . $this->login_time);
            $logout = \Carbon\Carbon::parse($this->attendance_date . ' ' . $this->logout_time);
            return $logout->diffInHours($login);
        }
        return 0;
    }
}