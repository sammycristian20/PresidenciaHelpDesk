<?php

namespace App\Bill\Models;

use App\BaseModel;

class PackageDepartment extends BaseModel
{
    protected $table = 'package_department_link';
    
    protected $fillable = ['id', 
    /**
     * Foreign key refrencing to package table
     */
    'package_id', 
    
    /**
     * Foreign key refrencing to departments table
     */
    'department_id', 'created_at','updated_at'];

    public function package()
    {
    	return $this->belongsTo('App\Bill\Models\Package', 'package_id');
    }

    public function department()
    {
    	return $this->belongsTo('App\Model\helpdesk\Agent\Department', 'department_id');
    }
}
