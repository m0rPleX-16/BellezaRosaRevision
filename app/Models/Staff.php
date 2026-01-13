<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'user_id',
        'specialty',
        'color_code'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get formatted specialty display name
     */
    public function getFormattedSpecialtyAttribute(): string
    {
        $specialtyMap = [
            'hair' => 'Hair',
            'nail' => 'Nail',
            'spa' => 'Spa',
            'hair_nail' => 'Hair & Nail',
            'hair_spa' => 'Hair & Spa',
            'nail_spa' => 'Nail & Spa',
            'all' => 'All Services',
        ];

        return $specialtyMap[$this->specialty] ?? ucfirst(str_replace('_', ' & ', $this->specialty));
    }
}
