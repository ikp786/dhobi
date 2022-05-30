<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'minimum_order_value',
        'quantity_type',
        'description',
        'status'
    ];

    public function products()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }
}
