<?php

namespace App\Bill\Models;

use App\BaseModel;

class Order extends BaseModel
{
    protected $table = 'orders';
    
    protected $fillable = ['id', 'package_id', 'user_id', 'credit_type', 'credit', 'status', 'expiry_date', 'created_at','updated_at'];


     public function package()
    {
        $related    = 'App\Bill\Models\Package';
        $foreignKey = 'package_id';
        return $this->belongsTo($related, $foreignKey);
    }

     public function user()
    {
        $related    = 'App\User';
        $foreignKey = 'user_id';
        return $this->belongsTo($related, $foreignKey);
    }

    public function orderTickets()
    {
        return $this->hasMany('App\Bill\Models\UserOrderTickets', 'order_id');
    }
    public function invoice()
    {
     return $this->hasOne('App\Bill\Models\Invoice');
    }
}
