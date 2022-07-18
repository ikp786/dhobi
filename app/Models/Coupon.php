<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = [
        'coupon_code',
        'minimum_order_amount',
        'discount_percentage',
        'max_discount_amount',
        'start_date',
        'end_date',
        'status',
        'description'
    ];
}
