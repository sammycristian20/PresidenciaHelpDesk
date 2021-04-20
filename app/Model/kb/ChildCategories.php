<?php

namespace App\Model\kb;

use App\BaseModel;

class ChildCategories extends BaseModel
{
    protected $table = 'kb_child_category';
    protected $fillable = ['id','slug', 'name', 'description', 'status', 'parent_category_id', 'display_order','visible_to','created_at', 'updated_at'];

     public function articles()
     {
        return $this->hasMany('App\Model\kb\Relationship');
     }



     public function parentCategory()
     {
    	return $this->belongsTo('App\Model\kb\ParentCategory', 'parent_category_id', 'id');
     }
}
