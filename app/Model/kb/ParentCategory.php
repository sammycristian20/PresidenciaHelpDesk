<?php

namespace App\Model\kb;

use App\BaseModel;

class ParentCategory extends BaseModel
{
    protected $table = 'kb_parent_category';
    protected $fillable = ['id', 'name','slug','created_at', 'updated_at'];

    
      public function childCategory()
    {
        return $this->hasMany('App\Model\kb\ChildCategory');
    }


    
}
