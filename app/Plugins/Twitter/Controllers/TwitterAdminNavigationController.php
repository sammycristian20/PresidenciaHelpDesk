<?php
namespace App\Plugins\Twitter\Controllers;
use Illuminate\Support\Collection;
use Lang;
use App\Traits\NavigationHelper;
/**
 * Handles Admin Navigation for Twitter plugin
 */
class TwitterAdminNavigationController
{
  use NavigationHelper;
  /**
   * Injects Twitter specific navigation to core admin navigation
   * @param Collection $coreNavigationArray
   * @return null
   */
  public function injectTwitterAdminNavigation(Collection &$coreNavigationContainer)
  {
    $navigationArray = $this->getNavigationArray();
    $coreNavigationContainer->push(
      $this->createNavigationCategory(Lang::get('Twitter::lang.twitter'), $navigationArray)
    );
  }
  /**
   * Gets Navigation array which with all the navigations comes under helpdesk admin panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();
    $this->injectNavigationIntoCollection($navigationArray, 'twitter_settings', 'fab fa-twitter','twitter/settings','twitter/settings');
    return $navigationArray;
  }
  private function injectNavigationIntoCollection(Collection &$navigationArray, string $name, string $iconClass, string $redirectUrl, string $routeString)
  {
    $name = Lang::get("Twitter::lang.$name");
    $navigationArray->push(
      $this->getNavigationObject($name, $iconClass, $redirectUrl, $routeString)
    );
  }
}