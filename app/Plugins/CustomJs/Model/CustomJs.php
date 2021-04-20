<?php

namespace App\Plugins\CustomJs\Model;

use Illuminate\Database\Eloquent\Model;

class CustomJs extends Model
{

    protected $table = 'customjs';

    protected $fillable = ['id', 'name', 'parameter', 'fired_at', 'script', 'fire', 'created_at', 'updated_at'];

    public function getFireAttribute($value)
    {
        return (int)$value;
    }

}
