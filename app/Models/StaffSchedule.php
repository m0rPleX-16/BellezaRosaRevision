<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffSchedule extends Model
{
    protected $fillable = [
        'staff_id',
        'day_of_week',
        'start_time',
        'end_time',
        'max_appointments',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'max_appointments' => 'integer',
    ];

    /**
     * Get the staff that owns this schedule
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get formatted day name
     */
    public function getFormattedDayAttribute(): string
    {
        return ucfirst($this->day_of_week);
    }

    /**
     * Get formatted time range
     */
    public function getFormattedTimeRangeAttribute(): string
    {
        $start = \Carbon\Carbon::parse($this->start_time)->format('g:i A');
        $end = \Carbon\Carbon::parse($this->end_time)->format('g:i A');
        return "{$start} - {$end}";
    }

    /**
     * Get formatted appointment limit
     */
    public function getFormattedLimitAttribute(): string
    {
        if ($this->max_appointments === null) {
            return 'Unlimited';
        }
        return "Max {$this->max_appointments} appointments/day";
    }
}
