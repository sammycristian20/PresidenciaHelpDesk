<?php

namespace App\Model\kb;

use App\BaseModel;

class Page extends BaseModel
{
    protected $table = 'kb_pages';

    protected $fillable = ['name', 'slug', 'status', 'visibility', 'description','seo_title','param_link','meta_description'];

    protected $htmlAble = ['description'];
}
