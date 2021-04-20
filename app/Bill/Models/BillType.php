<?php

namespace App\Bill\Models;

use Illuminate\Database\Eloquent\Model;

class BillType extends Model
{
    protected $table = 'bill_type';
    protected $fillable = ['type','price'];
    
    public function types(){
        return $this->belongsTo('App\Model\helpdesk\Manage\Tickettype','type');
    }
}
