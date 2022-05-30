<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'razorpay_id',
        'is_order',
        'address_id',
        'address',
        'remark',
        'pincode',
        'order_amount',
        'email',
        'mobile',
        'total_product_amount',
        'add_on_service_amount',
        'deliver_charge',
        'coupon_code',
        'coupon_amount',
        'user_id',
        'device_token',
        'driver_id',
        'txn_id',
        'payment_method',
        'payment_status',
        'order_delivery_status',
        'driver_payment_type',
        'delivery_date',
        'pickup_date',
        'pickup_time_slot_id',
        'delivery_time_slot_id',
        'pickup_time',
        'delivery_time',
    ];

    function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    function drivers()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }

    function addresses()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    function orderProduct()
    {
        return $this->hasOne(OrderProduct::class, 'order_id', 'id');
    }

    function orderProductList()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }
}
