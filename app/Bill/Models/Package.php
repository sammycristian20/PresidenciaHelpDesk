<?php

namespace App\Bill\Models;

use App\BaseModel;

class Package extends BaseModel
{
    protected $table = 'packages';
    
    protected $fillable = ['id', 'name', 'description', 'status', 'price', 'validity', 'allowed_tickets', 'display_order', 'package_pic','kb_link','created_at','updated_at'];

    public function packageDepartment()
    {
    	return $this->hasMany('App\Bill\Models\PackageDepartment', 'package_id', 'id');
    }
}
