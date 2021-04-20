<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\kb\SearchRequest;
use App\Model\kb\Article;
use App\Model\kb\Category;
use App\Model\kb\Relationship;
use App\Model\kb\Settings;
use Auth;
use Lang;
use Illuminate\Http\Request;
use App\Model\kb\Comment;
use App\Model\kb\Page;
use App\Model\helpdesk\Filters\Tag;
use App\Model\kb\KbArticleTag;

class KnowledgeBaseController extends Controller {

    /**
     * function to search an article.
     *
     * @param \App\Http\Requests\kb\SearchRequest $request
     * @param \App\Model\kb\Article               $article
     *
     * @return json
     */
    public function search(SearchRequest $request)
    {
        $pagination = Settings::first()->pagination;
        $allowedArticles = $this->getArticleListForUser();
        $searchString = urldecode($request->input('s'));
        $currentTime = date("Y-m-d H:i:s");
        // dd($allowedArticles);
        $baseQuery = Article::where('type', 1)
          ->whereIN('kb_article.visible_to', $allowedArticles)
          ->where('kb_article.publish_time', '<', $currentTime);

        $this->appendKbSearchAlgorithm($searchString, $baseQuery);

        $articles = $baseQuery->paginate($pagination);

        return successResponse('', ['articles' => $articles]);
    }

    /**
     * Appends search queries to base query provided
     * Algorithm : each seach result gets a relavance point. Based on that we sort it in
     *         descending order. Below is the relavance logic
     *         1. if full sentence matches in the article title, it will get 5 points
     *         2. if individual words matches in the article title, it will get 4 points
     *         3. if full sentence matches in the category description, it will get 2 points
     *         4. if individual words matches in the category description, it will get 1 point
     *         5. if individual words matches in category name it will get 0.5 points
     *         6. if individual words matches in category description it will get 0.2 points
     *
     * Not using named parameters because it requires additional settings for using same named parameter 2 multiple times
     * @see https://github.com/laravel/framework/issues/12715
     *
     * @param  string $searchString
     * @return Collection
     */
    public function appendKbSearchAlgorithm(string $searchString, &$baseQuery)
    {

        $sanitizedInputForFullTextSearch = $this->getSanitizedInputForFullTextSearch($searchString);

        $searchStringForWordSearch = "([[:blank:][:punct:]]|^)".$searchString."([[:blank:][:punct:]]|$)";

        $baseQuery = $baseQuery
            ->join('kb_article_relationship','kb_article_relationship.article_id','=','kb_article.id')
            ->join('kb_category', 'kb_article_relationship.category_id','=','kb_category.id')

            ->where(function($query)use($searchString, $sanitizedInputForFullTextSearch){
                $query->whereRaw("(MATCH (kb_article.name) AGAINST (? IN BOOLEAN MODE))", $sanitizedInputForFullTextSearch)
                    ->orWhereRaw("(MATCH (kb_article.description) AGAINST (? IN BOOLEAN MODE))", $sanitizedInputForFullTextSearch)
                    ->orWhereRaw("(MATCH (kb_category.name) AGAINST (? IN BOOLEAN MODE))", $sanitizedInputForFullTextSearch)
                    ->orWhereRaw("(MATCH (kb_category.description) AGAINST (? IN BOOLEAN MODE))", $sanitizedInputForFullTextSearch)
                    ->orWhere("kb_article.name", "LIKE", "%$searchString%")
                    ->orWhere("kb_article.description", "LIKE", "%$searchString%");
            })
            ->selectRaw("kb_article.id, kb_article.name, kb_article.description, publish_time, kb_article.slug, kb_article.type,
            (
                5 * (MATCH (kb_article.name) AGAINST (? IN BOOLEAN MODE)) + 5 * (CASE WHEN kb_article.name LIKE ? THEN 1 ELSE 0 END) +
                
                4 * (CASE WHEN kb_article.name REGEXP ? THEN 1 ELSE 0 END) + 4 * (MATCH (kb_article.name) AGAINST (? IN BOOLEAN MODE)) +

                2 * (MATCH (kb_article.description) AGAINST (? IN BOOLEAN MODE)) + 2 * (CASE WHEN kb_article.description LIKE ? THEN 1 ELSE 0 END) +
                
                1 * (CASE WHEN kb_article.description REGEXP ? THEN 1 ELSE 0 END) + 1 * (MATCH (kb_article.description) AGAINST (? IN BOOLEAN MODE)) +
                
                0.5 * (MATCH (kb_category.name) AGAINST (? IN BOOLEAN MODE)) + 0.2 * (MATCH (kb_category.description) AGAINST (? IN BOOLEAN MODE))
                
            ) AS relevance", [
                "$sanitizedInputForFullTextSearch", "%$searchString%",

                $searchStringForWordSearch, $sanitizedInputForFullTextSearch,

                "$sanitizedInputForFullTextSearch", "%$searchString%",

                $searchStringForWordSearch, $sanitizedInputForFullTextSearch,

                $sanitizedInputForFullTextSearch, $sanitizedInputForFullTextSearch,
            ])
            ->orderBy('relevance', 'DESC')
            ->groupBy('kb_article.id')
            ->orderBy('kb_article.created_at', 'DESC');
    }

    /**
     * Sanitizes input to avoid characters which are not supported in mysql full-text search
     * @param string $searchString
     * @return string|string[]\
     */
    private function getSanitizedInputForFullTextSearch(string $searchString)
    {
        return str_replace(["@", "*"], " ", $searchString);
    }

    /**
     * To get the category list with article count. based on category link with an article
     * @param $category an instance of Category
     * @return array
     */
    public function getCategoryListWithArticleCount(Category $category)
    {
        //get catagory ids which is link with article
        $categoryIds = getPrioritiesValueAsArray(new Relationship(), 'category_id');
        //get all category ids 
        $baseCategoryIds = getPrioritiesValueAsArray(new Category(), 'id');

        $categoryIds = array_unique(array_merge($categoryIds,$baseCategoryIds));

        $idsOrdered = implode(',', $categoryIds);

        $currentTime = date("Y-m-d H:i:s");

        $allowedArticles = $this->getArticleListForUser();

        $baseQuery = $category->where('status', 1)
                ->whereIn('id', $categoryIds)
                //->orderByRaw(\DB::raw("FIELD(id, $idsOrdered)"))
                ->select('id', 'name', 'slug')
                ->withCount(['articles' => function($q) use($currentTime, $allowedArticles) {
                $q->where('type', '1')->where('publish_time', '<', $currentTime)->whereIN('visible_to', $allowedArticles);
            }]);


        if ($idsOrdered) {

            $baseQuery = $baseQuery->orderByRaw(\DB::raw("FIELD(id, $idsOrdered)"));
        }

        return successResponse('', ['categories' => $baseQuery->get()]);
    }

    /**
     * to get category list with article names.
     * @param $category an instance of Category
     * @return array
     */
    public function getCategoryListWithArticles1(Category $category)
    {

        $allowedArticles = $this->getArticleListForUser();
        $currentTime = date("Y-m-d H:i:s");
        $result = $category->where('status', 1)
                        ->select('id', 'name', 'slug', 'created_at', 'updated_at')
                        ->with(['articles' => function($query) use ($currentTime, $allowedArticles) {
                                $query->whereIN('visible_to', $allowedArticles)
                                ->where('type', '1')
                                ->where('publish_time', '<', $currentTime)
                                ->select('kb_article.id', 'kb_article.name', 'kb_article.slug');
                            }])->get();
        return successResponse('', ['categories' => $result]);
    }

    /**
     * to get category list with article names.
     * @param $category an instance of Category
     * @return array
     */
    public function getCategoryListWithArticles(Category $category)
    {

        $allowedArticles = $this->getArticleListForUser();
        $currentTime = date("Y-m-d H:i:s");
        $pagination = Settings::first()->pagination;
        $result = $category->where('status', 1)
                        ->select('id', 'name', 'slug', 'created_at', 'updated_at')
                        ->with(['articles' => function($query) use ($currentTime, $allowedArticles) {
                                $query->whereIN('visible_to', $allowedArticles)
                                ->where('type', '1')
                                ->orderBy('publish_time','desc')
                                ->where('publish_time', '<', $currentTime)
                                ->select('kb_article.id', 'kb_article.name', 'kb_article.slug');
                            }])->orderBy('display_order','asc')->paginate($pagination);
        return successResponse('', ['categories' => $result]);
    }

    /**
     * to get category list with article names.
     * @param $article an instance of Article
     * @return json
     */
    public function getArticleListWithCategories(Article $article,Request $request)
    {
        $tagId = $request->tag_id;
        $tagInfo = NULL;

        if($tagId){

            $tagInfo = Tag::where('id',$tagId)->select('id','name')->first();

            $articleIds = KbArticleTag::where('tag_id',$tagId)->pluck('article_id')->toArray();

            $article = $article->whereIN('id',$articleIds);
        }

        $pagination = Settings::first()->pagination;
        $allowedArticles = $this->getArticleListForUser();
        $currentTime = date("Y-m-d H:i:s");
        $articles = $article->with(['categories' => function($q) {
                        $q->select('kb_category.name', 'kb_category.id');
                    }])
                    ->whereIN('visible_to', $allowedArticles) 
                    ->orderBy('publish_time','desc')
                    ->where('publish_time', '<', $currentTime)
                    ->where('type', '1')
                    ->select('id', 'name', 'description', 'publish_time', 'created_at', 'updated_at', 'slug')
                    ->paginate($pagination);

        return successResponse('', ['tag' => $tagInfo,'articles' => $articles]);
    }

    /**
     * to get category list with article names.
     * @param $article an instance of Article
     * @return array
     */
    public function getArticleListWithCategories1(Article $article)
    {
        $allowedArticles = $this->getArticleListForUser();
        $currentTime = date("Y-m-d H:i:s");
        $articles = $article->with(['categories' => function($q) {
                        $q->select('kb_category.name', 'kb_category.id');
                    }])->whereIN('visible_to', $allowedArticles) ->where('publish_time', '<', $currentTime)->where('type', '1')->select('id', 'name', 'description', 'publish_time', 'created_at', 'updated_at', 'slug')->get();

        return successResponse('', ['articles' => $articles]);
    }

    /**
     * gets articles for a given category
     * @param $categoryId
     * @param $category an instance of Category
     * @return array
     */
    public function getArticlesForCategory($categoryId, Category $category)
    {
        $currentTime = date("Y-m-d H:i:s");
        $allowedArticles = $this->getArticleListForUser();
        $pagination = Settings::first()->pagination;
    
        $result = $category->where('id', $categoryId)
                    ->select('id', 'name', 'slug', 'created_at', 'updated_at')
                    ->with(['articles' => function($query) use ($currentTime, $allowedArticles) {
                            $query->whereIN('visible_to', $allowedArticles)
                            ->where('type', '1')
                            ->where('publish_time', '<', $currentTime);
                    }])->first();


        if(!$result) 
            return errorResponse( Lang::get('lang.category_not_found'),404);

        $result = $result->toArray();

        if (count($result['articles']) > 0) {
            foreach ($result['articles'] as $key => $value) {
                $articleIds[] = $value['id'];
            }
            $article = Article::whereIn('id', $articleIds)->orderBy('publish_time','desc')->paginate($pagination);
        } else {
            $article = [];
        }
        
        return successResponse('', ['category' => $result['name'], 'article' => $article]);
        
    }

    /**
     * gets article for a given getArticleBySlug
     * @param string $articleSlug articleId
     * @param $article an instance of Article
     * @return json
     */
    public function getArticleBySlug($articleSlug, Article $article)
    {
        $currentTime = date("Y-m-d H:i:s");
        $pagination = Settings::first()->pagination;
        $articleId = Article::where('slug',$articleSlug)->value('id');
        $allowedArticles = $this->getArticleListForUser();
        if(!$articleId){
             return errorResponse( Lang::get('lang.article_not_found'),404);

        }
        $result = $article->where('id', $articleId)->whereIN('visible_to', $allowedArticles)
                        ->where('type', '1')
                        ->where('publish_time', '<', $currentTime)
                        ->with(['categories' => function($query) {
                                $query->select('kb_category.id', 'kb_category.name','kb_category.slug');
                            },
                            'tags' => function($query) {
                                $query->select('tags.id', 'tags.name');
                            }
                        ])->first();
        $comments = (!Auth::guest() && Auth::user()->role == 'admin') ? Comment::where('article_id', $articleId)->orderBy('created_at', 'desc')->paginate($pagination) : Comment::where('article_id', $articleId)->where('status', '1')->orderBy('created_at', 'desc')->paginate($pagination);


        return successResponse('', ['article' => $result, 'comments' => $comments]);
    }

    /**
     * gets category list for the logged in user
     * takes care of whether a user is allowed to see a category or not
     *
     * @return array
     */
    protected function getArticleListForUser()
    {
        $kbSettings = Settings::where('id', 1)->first();

        //kb accessibility if turned off
        if ($kbSettings->status != 1) {
            return [];
        }
        $loggedInUser = Auth::user();
        if (!$loggedInUser) {
            return ['all_users'];
        }
        if ($loggedInUser->role == 'user') {
            return ['logged_in_users', 'all_users', 'logged_in_users_and_agents'];
        }
        if ($loggedInUser->role == 'agent') {
            return ['all_users', 'agents', 'logged_in_users_and_agents'];
        }
        return ['all_users', 'logged_in_users', 'agents', 'logged_in_users_and_agents'];
    }

    /**
     * This method return page detals
     *
     * @return json
     */

    public function pageDetails(Request $request){

        $page = Page::where('slug', $request->slug)->select('name','description')->first();
        return successResponse('',  $page);
    }

    /**
     * to get tag list.
     * @param $kbTag an instance of Tag
     * @return array
     */
    public function getTagList(Tag $kbTag)
    {

        $modelObject = new KbArticleTag();

        $tagIds = getPrioritiesValueAsArray($modelObject, 'tag_id');

        $result = [];

        if ($tagIds) {

            $idsOrdered = implode(',', $tagIds);

            $result = $kbTag
                    ->whereIn('id', $tagIds)
                    ->orderByRaw(\DB::raw("FIELD(id, $idsOrdered)"))
                    ->select('id', 'name')
                    ->get();
        }


        return successResponse('', ['tags' => $result]);
    }

}
