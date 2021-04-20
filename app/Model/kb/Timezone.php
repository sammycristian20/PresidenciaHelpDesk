<?php

namespace App\Model\kb;

use App\BaseModel;

class Timezone extends BaseModel
{
    protected $table = 'timezone';

    protected $fillable = ['id', 'name', 'location'];

    public $timestamps = false;
}
