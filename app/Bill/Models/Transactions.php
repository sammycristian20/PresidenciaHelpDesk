<?php

namespace App\Bill\Models;

use App\BaseModel;

class Transactions extends BaseModel
{
    protected $table = 'transactions';
    
    protected $fillable = ['id', 'invoice_id', 'transactionId', 'payment_method', 'amount_paid', 'user_id','comment', 'status', 'created_at','updated_at'];

    public function paidBy()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function setCommentAttribute($value)
    {
    	$this->attributes['comment'] = htmlentities($value);
    }
}
