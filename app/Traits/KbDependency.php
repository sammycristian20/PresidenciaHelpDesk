<?php

namespace App\Traits;

use Exception;
use App\Model\kb\Category;
use App\Model\kb\ArticleTemplate;
use App\Model\helpdesk\Filters\Tag;
use Illuminate\Http\Request;

/**
 * Handles all kb dependency 
 */
trait KbDependency {

/**
     * gives array of Category
     * @return array            array of categories
     */
    protected function categories()
    {
        $baseQuery = Category::where('status', 1)->where('name', 'LIKE', "%$this->searchQuery%")->orderBy('id','desc');

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('categories', $baseQuery);
    }

    /**
     * gives array of ArticleTemplate
     * @return array            array of articleTemplates
     */
    protected function articleTemplates()
    {
        $baseQuery = ArticleTemplate::where('status', 1)->where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }
        return $this->get('articletemplates', $baseQuery);
    }
}
