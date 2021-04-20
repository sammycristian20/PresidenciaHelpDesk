<?php

namespace App\Plugins\Calendar\Controllers;

use Illuminate\Support\Collection;
use Lang;
use App\Traits\NavigationHelper;

/**
 * Handles Admin Navigation for service desk
 * @author Phaniraj K <phaniraj.k@ladybirdweb.com>
 */
class CalendarAdminNavigationController
{
  use NavigationHelper;

  /**
   * Injects service desk specific navigation to core agent navigation
   * @param Collection $coreNavigationArray
   * @return null
   */
  public function injectCalendarAdminNavigation(Collection &$coreNavigationContainer)
  {
    $navigationArray = $this->getNavigationArray();

    $coreNavigationContainer->push(
      $this->createNavigationCategory(Lang::get('Calendar::lang.tasks'), $navigationArray)
    );
  }

  /**
   * Gets Navigation array which with all the navigations comes under helpdesk agent panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();

    $this->injectNavigationIntoCollection($navigationArray, 'task-plugin-project-and-category-settings', 'fas fa-cog','tasks/settings','tasks/settings');

    $this->injectNavigationIntoCollection($navigationArray, 'task-plugin-template-settings', 'fas fa-tasks','tasks/template/settings','tasks/template/settings');


      return $navigationArray;
  }

  private function injectNavigationIntoCollection(Collection &$navigationArray, string $name, string $iconClass, string $redirectUrl, string $routeString)
  {
    $name = Lang::get("Calendar::lang.$name");
    $navigationArray->push(
      $this->getNavigationObject($name, $iconClass, $redirectUrl, $routeString)
    );
  }
}
