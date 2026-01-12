<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'username',
        'phone',
        'email',
        'gender',
        'birth_date',
        'notes',
        'total_visits',
        'total_spent',
        'last_visit'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_visit' => 'datetime',
        'total_visits' => 'integer',
        'total_spent' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}