<?php

namespace App\Bill\Models;

use App\BaseModel;
use App\Bill\Models\Transactions;

class Invoice extends BaseModel
{
    protected $table = 'invoices';
    
    protected $fillable = ['id', 'order_id', 'total_amount', 'tax','discount','payable_amount','due_by','amount_paid','paid_date', 'payment_mode', 'created_at','updated_at'];


    public function order()
    {
        $related    = 'App\Bill\Models\Order';
        $foreignKey = 'order_id';
        return $this->belongsTo($related, $foreignKey);
    }

    public function transactions()
    {
     return $this->hasMany('App\Bill\Models\Transactions');
    }
}
