<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOnServiceMappingInCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'add_on_service_id',
        'user_id',
        'device_token'
    ];

    function addOnService()
    {
        return $this->hasOne(AddOnService::class, 'id', 'add_on_service_id');
    }
}
