<?php

namespace App\Model\kb;

use App\BaseModel;

class Category extends BaseModel
{
    protected $table = 'kb_category';

    protected $fillable = ['id', 'slug', 'name', 'description', 'status', 'parent_id', 'display_order','created_at', 'updated_at'];

    /**
     * Attributes which requires html purification. All attributes which allows HTML should be added to it
     * @var array
     */
    protected $htmlAble = ['description'];

 	public function articles()
    {
        return $this->belongsToMany('App\Model\kb\Article','kb_article_relationship');
    }
    
    //belongs to parent
    public function children()
    {
        return $this->hasMany('App\Model\kb\Category','parent_id','id');
    }
    
    //belongs to a child
    public function parent()
    {
        return $this->hasOne('App\Model\kb\Category','parent_id','id');
    }
}