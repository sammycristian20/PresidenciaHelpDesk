<?php

namespace App\Model\kb;

use App\BaseModel;

class ArticleTemplate extends BaseModel
{
    protected $table = 'kb_article_template';
    protected $fillable = ['id', 'name', 'description', 'status','created_at', 'updated_at'];
}
