<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JarRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_id',
        'record_date',
        'total_refilling',
        'empty_jars',
        'onboard_jars',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'record_date' => 'date',
    ];

    /**
     * Get the supplier that owns the jar record.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get total jars (refilling + empty + onboard).
     */
    public function getTotalJarsAttribute()
    {
        return $this->total_refilling + $this->empty_jars + $this->onboard_jars;
    }
}