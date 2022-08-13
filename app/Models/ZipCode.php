<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZipCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'zipcode',
        'delivery_charge',
        'minimum_order_value',
        'status'
    ];
}
