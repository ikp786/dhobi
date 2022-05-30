<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'user_id',
        'ratting_star',
        'ratting_comment'
    ];
}
