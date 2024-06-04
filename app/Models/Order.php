<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    function order_items(){
        return $this->hasMany(OrderItem::class);
    }

    function user(){
        return $this->belongsTo(User::class);
    }

    function service(){
        return $this->belongsTo(Service::class);
    }

    function order_status(){
        return $this->belongsTo(OrderStatus::class);
    }
}
