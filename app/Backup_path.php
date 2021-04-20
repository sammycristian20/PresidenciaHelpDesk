<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Backup_path extends Model
{
    protected $table = 'backup_paths';
    protected $fillable = ['backup_path'];
}
