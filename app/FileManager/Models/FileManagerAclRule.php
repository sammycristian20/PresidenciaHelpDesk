<?php

namespace App\FileManager\Models;

use Illuminate\Database\Eloquent\Model;


class FileManagerAclRule extends Model
{
    protected $table = 'file_manager_acl_rules';

    protected $fillable = ['user_id','disk','path','access','type','dirname','basename','hidden'];

    public function departments()
    {
        return $this->hasMany(FileManagerAclRuleDepartment::class, 'rule_id');
    }
}

