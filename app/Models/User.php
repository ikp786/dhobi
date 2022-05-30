<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;
    // use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'role',
        'unique_id',
        'address',
        'dob',
        'online_status',
        'status',
        'profile_pic',
        'device_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function driverOrders()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function driverOrder()
    {
        return $this->hasOne(Order::class, 'driver_id');
    }

    public function address()
    {
        return $this->hasMany(Address::class);
    }

    public function ratting()
    {
        return $this->hasMany(Ratting::class);
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }
}
