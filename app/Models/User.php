<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Customer;
use App\Models\Notification;     

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'username',
        'phone',
        'email',
        'password',
        'role',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
    // Message relationships
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get the custom notifications for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    // Get unread messages count
    public function getUnreadMessagesCountAttribute()
    {
        return $this->receivedMessages()->unread()->count();
    }

    // Get unread notifications count
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->customNotifications()->unread()->count();
    }

    // Get total unread count
    public function getTotalUnreadCountAttribute()
    {
        return $this->unread_messages_count + $this->unread_notifications_count;
    }

    // Your existing relationships and methods...
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    protected static function booted()
    {
        static::created(function ($user) {
            if ($user->role === 'customer' && !$user->customer) {
                Customer::create([
                    'user_id' => $user->id,
                    'full_name' => $user->full_name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '0000000000',
                    'gender' => 'other',
                    'birth_date' => now()->subYears(18)->format('Y-m-d'),
                    'total_visits' => 0,
                    'total_spent' => 0,
                ]);
            }
        });
    }
}