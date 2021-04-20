<?php namespace App\Plugins\SMS\Model;

use Illuminate\Database\Eloquent\Model;

class Msg91 extends Model
{
    protected $table = 'sms';
    protected $fillable = ['provider_id','name','value'];
}
