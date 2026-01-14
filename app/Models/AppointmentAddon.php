<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentAddon extends Model
{
    protected $fillable = [
        'appointment_id',
        'service_id',
        'name',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    protected $appends = [
        'display_name',
        'formatted_price',
        'service_type_label',
        'service_type_color',
        'icon_class',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the display name (service name if available, otherwise custom name)
     */
    public function getDisplayNameAttribute()
    {
        return $this->service ? $this->service->name : $this->name;
    }

    /**
     * Check if this addon is based on a salon service
     */
    public function isServiceAddon(): bool
    {
        return !is_null($this->service_id);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Get service type label for display
     */
    public function getServiceTypeLabelAttribute(): string
    {
        return $this->isServiceAddon() ? 'Salon Service' : 'Custom';
    }

    /**
     * Get service type color class for display
     */
    public function getServiceTypeColorAttribute(): string
    {
        return $this->isServiceAddon() ? 'purple' : 'gray';
    }

    /**
     * Get icon class for display
     */
    public function getIconClassAttribute(): string
    {
        return $this->isServiceAddon() ? 'fas fa-spa' : 'fas fa-plus-circle';
    }
}
