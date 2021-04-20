<?php

namespace App\Model\helpdesk\Agent;

use Illuminate\Database\Eloquent\Model;

/**
 * UserPermission model  
 */
class UserPermission extends Model
{
    protected $table = 'user_permissions';
    protected $fillable = ['key', 'name', 'type'];

}
