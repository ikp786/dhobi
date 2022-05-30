<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected  $fillable = [
        'order_id',
        'product_id',
        'category_id',
        'sub_category_id',
        'add_on_services',
        'total_amount',
        'add_on_service_amount',
        'product_name',
        'product_description',
        'product_quantity',
    ];

    function orders()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    function products()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
