<?php

namespace App\Traits;

use App\Http\Controllers\Common\Navigation\Navigation;
use App\Http\Controllers\Common\Navigation\NavigationCategory;
use Illuminate\Support\Collection;
use Lang;

/**
 * Contains all helper methods required by Navigation classes
 * @author avinash.kumar <avinash.kumar@ladybirdweb.com>
 */
trait NavigationHelper
{

  /**
   * Creates a Navigation Category which can be used for injecteing navigation array for a module/plugin
   * @param  string             $name
   * @param  Collection         $navigationArray
   * @return NavigationCategory
   */
  public function createNavigationCategory(string $name, Collection &$navigationArray) : NavigationCategory
  {
    $navigationCategory = new NavigationCategory;

    $navigationCategory->name = $name;

    $navigationCategory->setNavigations($navigationArray);

    return $navigationCategory;
  }

  /**
   * Creates navigation object with no children
   * @param Navigation $parentNavigation parent into which child has to be injected
   * @param  string $name        name of the navigation
   * @param  string $iconClass
   * @param  string $redirectUrl the url to which it should redirect
   * @param  string $routeString the string by which it has to be identified as active route
   * @return null
   */
  public function injectChildNavigation(Navigation &$parentNavigation, string $name, string $iconClass, string $redirectUrl, string $routeString)
  {
    $name = Lang::get("lang.$name");
    $parentNavigation->injectChildNavigation($this->getNavigationObject($name, $iconClass, $redirectUrl, $routeString));
  }

  /**
   * Creates navigation object with no children
   * @param  string $name        name of the navigation
   * @param  string $iconClass
   * @param  string $redirectUrl the url to which it should redirect
   * @param  string $routeString the string by which it has to be identified as active route
   * @return Navigation
   */
  public function getNavigationObject(string $name, string $iconClass, string $redirectUrl, string $routeString) : Navigation
  {
    $navigation = new Navigation;

    $navigation->setName($name);

    $navigation->setIconClass($iconClass);

    $navigation->setRedirectUrl($redirectUrl);

    $navigation->setRouteString($routeString);

    return $navigation;
  }

}
