<?php

namespace App\Http\Controllers\Client\helpdesk;

use App\Facades\Attach;
use App\Http\Controllers\Controller;
use App\Model\kb\Article;
use App\Model\kb\Page;
use \App\Model\helpdesk\Settings\Company;
use Lang;
use URL;
use Auth;
use Request;

/**
 * To append title and description in meta
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */
class SeoController extends Controller {

    /**
     * method to append title and description
     * @return response
     */
    public function getUrlandAppendTitleDescription()
    {
      $relativeUrl = str_replace(URL::to('/'), '', Request::url());
      $meta = $this->appendTitleAndDescriptionInMeta($relativeUrl);
      return successResponse('', $meta);
    }

    /**
     * method to append title and description
     * @param $relativeUrl
     * @return array [title, description, company_name]
     */
    public function appendTitleAndDescriptionInMeta($relativeUrl)
    {
        $slug = (!empty($relativeUrl)) ? basename($relativeUrl) : '';
        $title = $description = '';
        $this->checkArticle($title, $description, $slug);
        $this->checkPage($title, $description, $slug);
        if (empty($title) && empty($description)) {
          $relativeUrl = implode('/', array_filter(array_values(explode("/",$relativeUrl)), function($subUrl) {
            $subUrl = str_replace('-', '', $subUrl);
            if (!ctype_alpha($subUrl)) {
                return false;
            }
            return true;
          }));
          $this->checkOtherUrl($relativeUrl, $title, $description);
        }
        $company = Company::Where('id', '=', '1')->first();
        $companyName = is_null($company->company_name) || empty($company->company_name) ? 'Support Center' : $company->company_name;
        $companyLogo = !empty($company->logo) && $company->use_logo ? Attach::getUrlForPath($company->logo, $company->logo_driver) : '';
    
      return ['title' => $title, 'description' => $description, 'company_name' => $companyName, 'company_logo' => $companyLogo];
    }

    /**
     * method to check other url
     * @param $relativeUrl
     * @param $title
     * @param $description
     * @return
     */
    private function checkOtherUrl($relativeUrl, &$title, &$description)
    {
        $path = str_replace('/', '-', $relativeUrl);
        $title = Lang::get('lang.home-page-title');
        $description = Lang::get('lang.home-page-description');

        if (strpos(Lang::get('lang.' . $path . '-title'), 'lang.') === false) {
            $title = Lang::get('lang.' . $path . '-title');
            $description = Lang::get('lang.' . $path . '-description');
        }

    }

    /**
     * method to check article
     * @param $title
     * @param $description
     * @param $slug
     * @return
     */
    private function checkArticle(&$title, &$description, $slug)
    {
        $article = Article::where('slug', $slug)->first();
        if ($article) {
            $title = ($article->seo_title) ? str_limit($article->seo_title, 60) : str_limit($article->name, 60);
            $description = ($article->meta_description) ? str_limit($article->meta_description, 160) : str_limit($article->description, 160);
        } 

    }

    /**
     * method to check page
     * @param $title
     * @param $description
     * @param $slug
     * @return
     */
    private function checkPage(&$title, &$description, $slug)
    {
        $page = Page::where('slug', $slug)->first();
        if ($page) {
            $title = ($page->seo_title) ? str_limit($page->seo_title, 60) : str_limit($page->name, 60);
            $description = ($page->meta_description) ? str_limit($page->meta_description, 160) : str_limit($page->description, 160);
        }

    }

 }

