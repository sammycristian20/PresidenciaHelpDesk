<?php

namespace App\Model\kb;

use App\BaseModel;
use Nicolaslopezj\Searchable\SearchableTrait;
use Auth;

class Article extends BaseModel {

    use SearchableTrait;

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        'columns' => [
            'name' => 10,
            'slug' => 10,
            'description' => 10,
        ],
    ];

    /*  define the table name to get the properties of article model as protected  */
    protected $table = 'kb_article';

    /* define the fillable field in the table */
    protected $fillable = ['id', 'name', 'slug', 'description', 'type', 'status', 'template', 'seo_title', 'meta_description', 'publish_time', 'visible_to', 'author', 'created_at', 'updated_at','is_comment_enabled'];

    /**
     * Attributes which requires html purification. All attributes which allows HTML should be added to it
     * @var array
     */
    protected $htmlAble = ['description'];

    protected $dates = ['publish_time', 'created_at', 'updated_at'];

    public function categories()
    {
        return $this->belongsToMany('App\Model\kb\Category', 'kb_article_relationship', 'article_id', 'category_id');
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author');
    }

    public function allComments()
    {
        $instance = $this->hasMany('App\Model\kb\Comment', 'article_id', 'id');
        return $instance;
    }

    public function pendingComments()
    {
        $instance = $this->hasMany('App\Model\kb\Comment', 'article_id', 'id')->where('status', 0);
        return $instance;
    }

    public function getDescriptionAttribute($value)
    {
        //if user or guest try to access description inside remask tag content will not display else user will display
        if (Auth::guest() || Auth::user()->role == "user") {
          return preg_replace('@\[remark\](.*?)(?s).*\[\/remark\]@', '', $value);
        } 
        $value = preg_replace('@\[remark\]*@', '<div class="article-remark">', $value);
        return preg_replace('@\[\/remark\]*@', '</div>', $value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = clean($value);
    }
    public function tags()
    {
        return $this->belongsToMany('App\Model\helpdesk\Filters\Tag', 'kb_article_tag', 'article_id', 'tag_id');
    }
}
