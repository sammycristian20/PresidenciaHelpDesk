<?php

namespace App\Plugins\Facebook\Controllers;

use Illuminate\Support\Collection;
use Lang;
use App\Traits\NavigationHelper;

/**
 * Handles Admin Navigation for facebook plugin
 */
class FacebookAdminNavigationController
{
  use NavigationHelper;

  /**
   * Injects facebook specific navigation to core admin navigation
   * @param Collection $coreNavigationArray
   * @return null
   */
  public function injectFacebookAdminNavigation(Collection &$coreNavigationContainer)
  {
    $navigationArray = $this->getNavigationArray();

    $coreNavigationContainer->push(
      $this->createNavigationCategory(Lang::get('Facebook::lang.facebook_navigation_heading'), $navigationArray)
    );
  }

  /**
   * Gets Navigation array which with all the navigations comes under helpdesk admin panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();

    $this->injectNavigationIntoCollection($navigationArray, 'facebook_page_settings_nav', 'fab fa-facebook-square','facebook/settings','facebook/settings');
    $this->injectNavigationIntoCollection($navigationArray, 'facebook_security_settings_nav', 'fas fa-shield-alt','facebook/security-settings','facebook/security-settings');

    return $navigationArray;
  }

  private function injectNavigationIntoCollection(Collection &$navigationArray, string $name, string $iconClass, string $redirectUrl, string $routeString)
  {
    $name = Lang::get("Facebook::lang.$name");
    $navigationArray->push(
      $this->getNavigationObject($name, $iconClass, $redirectUrl, $routeString)
    );
  }
}
