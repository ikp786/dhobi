<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',        
        'price',        
        'category_id',
        'sub_category_id',
        'image',
        'status'
    ];

    public function categories()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }

    public function sub_categories()
    {
        return $this->hasOne(SubCategory::class,'id','sub_category_id');
    }

    public function images()
    {
        return $this->hasOne(ProductImage::class);
    }

    public function addOnServiceMappingInProduct()
    {
        return $this->hasMany(AddOnServiceMappingInProduct::class); 
    }
}