<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',        
        'category_id',
        'product_name',        
        'product_quantity',
        'product_amount',
        'total_amount',
        'device_token'
    ];

    public function addOnServiceMappingInCart()
    {
        return $this->hasMany(AddOnServiceMappingInCart::class); 
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getProductTotalPriceAttribute()
    {
        return $this->hasMany(Product::class)->select('price');
    }
    
}