<?php

namespace App\Plugins\Ldap\Controllers;

use Illuminate\Support\Collection;
use Lang;
use App\Traits\NavigationHelper;

/**
 * Handles Admin Navigation for service desk
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class LdapAdminNavigationController
{
  use NavigationHelper;

  /**
   * Injects service desk specific navigation to core agent navigation
   * @param Collection $coreNavigationArray
   * @return null
   */
  public function injectLdapAdminNavigation(Collection &$coreNavigationContainer)
  {
    $navigationArray = $this->getNavigationArray();

    $coreNavigationContainer->push(
      $this->createNavigationCategory(Lang::get('Ldap::lang.ldap'), $navigationArray)
    );
  }

  /**
   * Gets Navigation array which with all the navigations comes under helpdesk agent panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();

    $this->injectNavigationIntoCollection($navigationArray, 'ldap_settings', 'fas fa-server','ldap/settings','ldap/settings');

    return $navigationArray;
  }

  private function injectNavigationIntoCollection(Collection &$navigationArray, string $name, string $iconClass, string $redirectUrl, string $routeString)
  {
    $name = Lang::get("Ldap::lang.$name");
    $navigationArray->push(
      $this->getNavigationObject($name, $iconClass, $redirectUrl, $routeString)
    );
  }
}
