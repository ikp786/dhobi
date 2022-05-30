<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOnServiceMappingInProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'add_on_service_id'
    ];

    function addOnService(){
        return $this->hasOne(AddOnService::class,'id','add_on_service_id');
    }
}
