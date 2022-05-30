<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'order_id',
        'reason'
    ];

    function users()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
