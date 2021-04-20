<?php

namespace App\Model\Common;

use App\BaseModel;

class TemplateSet extends BaseModel
{
    protected $table = 'template_sets';
    protected $fillable = ['id', 'name', 'active', 'template_language', 'department_id'];

    /**
     * Relation with department
     */
    public function department()
    {
    	return $this->belongsTo(\App\Model\helpdesk\Agent\Department::class, 'department_id');
    }
}
