<?php namespace App\Plugins\SMS\Model;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $table='sms_service_providers';
    protected $fillable = ['name', 'created_at', 'updated_at'];
}
