<?php

namespace App\FileManager\Models;

use Illuminate\Database\Eloquent\Model;

class FileManagerAclRuleDepartment extends Model
{
    protected $table = 'file_manager_acl_rules_departments';

    protected $fillable = ['rule_id','department_id'];

    public function rule()
    {
        return $this->belongsTo(FileManagerAclRule::class, 'rule_id');
    }
}
