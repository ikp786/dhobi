<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponCartMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_code',
        'minimum_order_amount',
        'max_discount_amount',
        'device_token',
        'user_id',
        'cart_id'
    ];
}
