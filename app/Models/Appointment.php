<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Appointment extends Model
{
    protected $fillable = [
        'customer_id',
        'staff_id',
        'service_id',
        'service_level',
        'start_datetime',
        'end_datetime',
        'status',
        'payment_method',
        'total_amount',
        'notes',
        'is_walk_in',
        'cancellation_reason', // New: reason for cancellation
        'cancelled_by',        // New: user ID who cancelled
        'cancelled_at'         // New: timestamp of cancellation
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
        'is_walk_in'     => 'boolean',
        'total_amount'   => 'decimal:2',
        'cancelled_at'   => 'datetime', // New cast
        'service_level'  => 'string',
    ];

    protected $appends = [
        'base_price',
        'addons_total',
        'formatted_base_price',
        'formatted_addons_total',
        'service_level_display',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function addons()
    {
        return $this->hasMany(AppointmentAddon::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Relationship: The user who cancelled the appointment
     */
    public function cancelledByUser()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Check if appointment is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if appointment is marked as failed (e.g., payment failed, no-show)
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Cancel the appointment with reason and record who did it
     */
    public function cancel(string $reason, ?User $cancelledBy = null): void
    {
        $this->update([
            'status'             => 'cancelled',
            'cancellation_reason'=> $reason,
            'cancelled_by'       => $cancelledBy ? $cancelledBy->id : Auth::id(),
            'cancelled_at'       => now(),
        ]);

        // If there's a pending payment, cancel it too
        if ($this->payment && $this->payment->status === 'pending') {
            $this->payment->update(['status' => 'cancelled']);
        }
    }

    /**
     * Mark appointment as failed (e.g., payment failed or customer no-show)
     */
    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status'             => 'failed',
            'cancellation_reason'=> $reason,
            'cancelled_at'       => now(),
        ]);

        // Update payment status if exists
        if ($this->payment) {
            $this->payment->update(['status' => 'failed']);
        }
    }

    /**
     * Get the base service price (without addons)
     */
    public function getBasePriceAttribute(): float
    {
        if (!$this->service) {
            return 0;
        }

        return match($this->service_level) {
            'premium' => $this->service->price_premium ?? $this->service->price_regular,
            default => $this->service->price_regular,
        };
    }

    /**
     * Get the total addons price
     */
    public function getAddonsTotalAttribute(): float
    {
        return $this->addons->sum('price');
    }

    /**
     * Check if appointment has any addons
     */
    public function hasAddons(): bool
    {
        return $this->addons->count() > 0;
    }

    /**
     * Get formatted base price
     */
    public function getFormattedBasePriceAttribute(): string
    {
        return '₱' . number_format($this->base_price, 2);
    }

    /**
     * Get formatted addons total
     */
    public function getFormattedAddonsTotalAttribute(): string
    {
        return '₱' . number_format($this->addons_total, 2);
    }

    /**
     * Get formatted service level display
     */
    public function getServiceLevelDisplayAttribute(): string
    {
        return match($this->service_level) {
            'premium' => 'Premium',
            default => 'Regular',
        };
    }

    /**
     * Recalculate total amount based on service and addons
     */
    public function recalculateTotalAmount(): void
    {
        $this->update([
            'total_amount' => $this->base_price + $this->addons_total
        ]);
    }
}