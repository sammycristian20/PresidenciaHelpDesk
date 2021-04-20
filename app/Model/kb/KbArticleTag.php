<?php

namespace App\Model\kb;

use App\BaseModel;

class KbArticleTag extends BaseModel
{
    /* define the table  */

    protected $table = 'kb_article_tag';

    /* define fillable fields */
    
    protected $fillable = ['id', 'article_id', 'tag_id'];
}
