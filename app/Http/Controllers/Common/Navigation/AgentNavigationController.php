<?php

namespace App\Http\Controllers\Common\Navigation;

use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use App\Http\Controllers\Controller;
use Lang;
use Illuminate\Support\Collection;
use Auth;
use Event;
use App\Traits\NavigationHelper;
use App\User;

/**
 * Handles Agent panel Navigation
 * @author Avinash Kumar <avinash.kumar@ladybirdweb.com>
 */
class AgentNavigationController extends Controller
{

    public function __construct()
    {
        $this->middleware('role.agent');
    }

    use NavigationHelper;

  /**
   * Injects all ticket related navigations into parent ticket navigation
   * @return Response
   */
  public function getAgentNavigation()
  {
    $navigationArray = $this->getNavigationArray();

    $navigationContainer = collect();

    $navigationContainer->push($this->createNavigationCategory(Lang::get('lang.helpdesk'), $navigationArray));

    Event::dispatch('agent-panel-navigation-data-dispatch', [&$navigationContainer]);

    return successResponse('', $navigationContainer);
  }

  /**
   * Gets Navigation array which with all the navigations comes under helpdesk agent panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();

    $this->injectDashboardNavigation($navigationArray);

    $this->injectTicketNavigation($navigationArray);

    //below checking user and organization access permission based on side bar user tab will appear

    if(User::has('access_user_profile') || User::has('access_organization_profile')){

      $this->injectUsersNavigation($navigationArray);
    }

    $this->injectToolsNavigation($navigationArray);

    $this->injectReportsNavigation($navigationArray);

    $this->injectGoToAdminPanelNavigation($navigationArray);

    // $this->injectOldDashboardNavigation($navigationArray);

    $this->injectSignoutNavigation($navigationArray);

    return $navigationArray;
  }

  /**
   * Injects Logout Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectGoToAdminPanelNavigation(Collection $navigationArray)
  {
    if(Auth::user()->role == 'admin'){
      $navigationArray->push(
        $this->getNavigationObject(Lang::get('lang.go_to_admin_panel'), 'fas fa-level-up-alt', 'admin', '/admin')
      );
    }
  }

  /**
   * Injects Logout Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectSignoutNavigation(Collection $navigationArray)
  {
    $navigationArray->push(
       $this->getNavigationObject(Lang::get('lang.sign_out'), 'fas fa-sign-out-alt', 'auth/logout', 'auth/logout')
    );
  }

  /**
   * Injects ticket navigation into parent navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectTicketNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.tickets'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fas fa-ticket-alt');

    $navigationObject->setHasChildren(true);

    $ticketNavigation = new TicketNavigationController(new TicketListController);

    $ticketNavigation->injectTicketNavigation($navigationObject);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects ticket navigation into parent navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectDashboardNavigation(Collection &$navigationArray)
  {
    $navigationArray->push(
       $this->getNavigationObject(Lang::get('lang.dashboard'), 'fas fa-tachometer-alt', 'dashboard', 'dashboard')
    );
  }

  /**
   * Injects ticket navigation into parent navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectUsersNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.users'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fas fa-users');

    $navigationObject->setHasChildren(true);

    //if agent have user access permission then button will appar
    if(User::has('access_user_profile')){

    $this->injectChildNavigation($navigationObject, 'user_directory', 'far fa-circle', 'user', 'user');

    }
    
    //if agent have organization access permission then button will appar

    if(User::has('access_organization_profile'))
    {

    $this->injectChildNavigation($navigationObject, 'organization', 'far fa-circle', 'organizations', 'organizations');

    }
    $navigationArray->push($navigationObject);
  }

  /**
   * Injects ticket navigation into parent navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectReportsNavigation(Collection &$navigationArray)
  {

    if(User::has('report')){

        $navigationObject = new Navigation;

        $navigationObject->setName(Lang::get('lang.reports'));

        $navigationObject->setHasCount(false);

        $navigationObject->setIconClass('fas fa-chart-line');

        $navigationObject->setHasChildren(true);

        $this->injectChildNavigation($navigationObject, 'helpdesk_reports', 'far fa-circle', 'report/get', 'report/get');

        // this event can be used go inject more navigations by other plugins
        Event::dispatch('agent-panel-report-navigation-data-dispatch', [&$navigationObject]);

        $this->injectChildNavigation($navigationObject, 'activity_downloads', 'far fa-circle', 'report/activity-download', 'report/activity-download');

        if(Auth::user()->role == "admin"){
            $this->injectChildNavigation($navigationObject, 'settings', 'fas fa-wrench', 'report/settings', 'report/settings');
        }

        $navigationArray->push($navigationObject);
    }
  }

  /**
   * Injects ticket navigation into parent navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectToolsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.tools'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fas fa-wrench');

    $navigationObject->setHasChildren(true);

    $this->injectChildNavigation($navigationObject, 'canned_response', 'far fa-circle', 'canned/list', 'canned');

    $this->injectKnowledgeBaseNavigation($navigationObject);

    $this->injectRecurNavigation($navigationObject);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects recur navigation into tools navigation
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectRecurNavigation(&$navigationObject)
  {
    (User::has('recur_ticket')) ? $this->injectChildNavigation($navigationObject, 'recur', 'far fa-circle', 'agent/recur/list', 'recur') : '';
  }


  /**
   * Adds knowledgebase navigation which includes Category, Article, Pages, Comments, Settings
   * @param Collection $navigationArray
   * @return null
   */
  private function injectKnowledgeBaseNavigation(Navigation &$navigation)
  {
    if(User::has('access_kb')){

      $navigationObject = new Navigation;

      $navigationObject->setName(Lang::get('lang.knowledge_base'));

      $navigationObject->setHasCount(false);

      $navigationObject->setIconClass('fas fa-book');

      $navigationObject->setHasChildren(true);

      $navigationObject->injectChildNavigation($this->getCategoriesNavigation());

      $navigationObject->injectChildNavigation($this->getArticlesNavigation());

      $navigationObject->injectChildNavigation($this->getPagesNavigation());

      $this->injectChildNavigation($navigationObject, 'comments', 'fas fa-comments', 'comment', 'comment');

      $this->injectChildNavigation($navigationObject, 'settings', 'fas fa-wrench', 'kb/settings', 'kb/settings');

      $navigation->injectChildNavigation($navigationObject);
    }
  }

  /**
   * Gets Category Navigation
   * @return Navigation
   */
  private function getCategoriesNavigation()
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.categories'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fas fa-list-ul');

    $navigationObject->setHasChildren(true);

    $this->injectChildNavigation($navigationObject, 'addcategory', 'far fa-circle', 'category/create', 'category/create');

    $this->injectChildNavigation($navigationObject, 'allcategory', 'far fa-circle', 'category', 'category');

    return $navigationObject;
  }

  /**
   * Gets Article Navigation
   * @return Navigation
   */
  private function getArticlesNavigation()
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.articles'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('fas fa-edit');

    $navigationObject->setHasChildren(true);

    $this->injectChildNavigation($navigationObject, 'addarticle', 'far fa-circle', 'article/create', 'article/create');

    $this->injectChildNavigation($navigationObject, 'allarticle', 'far fa-circle', 'article', 'article');

    $this->injectChildNavigation($navigationObject, 'addarticletemplate', 'far fa-circle', 'article/create/template', 'article/create/template');

    $this->injectChildNavigation($navigationObject, 'allarticletemplate', 'far fa-circle', 'article/alltemplate/list', 'article/alltemplate/list');

    return $navigationObject;
  }

  /**
   * Gets Pages Navigation
   * @return Navigation
   */
  private function getPagesNavigation()
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.pages'));

    $navigationObject->setHasCount(false);

    $navigationObject->setIconClass('far fa-file-alt');

    $navigationObject->setHasChildren(true);

    $this->injectChildNavigation($navigationObject, 'addpages', 'far fa-circle', 'page/create', 'page/create');

    $this->injectChildNavigation($navigationObject, 'allpages', 'far fa-circle', 'page', 'page');

    return $navigationObject;
  }

    /**
    //  * Injects old dashboard navigation into parent navigation
    //  * @param  Collection &$navigationArray
    //  * @return null
    //  */
    // private function injectOldDashboardNavigation(Collection &$navigationArray)
    // {
    //     $navigationObject = $this->getNavigationObject(Lang::get('lang.old_dashboard'), 'fas fa-tachometer-alt', 'dashboard-old-layout', 'dashboard-old-layout');
    //     $navigationArray->push($navigationObject);
    // }

}
