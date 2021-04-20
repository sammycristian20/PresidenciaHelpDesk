<?php
namespace App\Plugins\Whatsapp\Controllers;
use Illuminate\Support\Collection;
use Lang;
use App\Traits\NavigationHelper;
/**
 * Handles Admin Navigation for Whatsapp plugin
 */
class WhatsappAdminNavigationController
{
  use NavigationHelper;
  /**
   * Injects Whatsapp specific navigation to core admin navigation
   * @param Collection $coreNavigationArray
   * @return null
   */
  public function injectWhatsappAdminNavigation(Collection &$coreNavigationContainer)
  {
    $navigationArray = $this->getNavigationArray();
    $coreNavigationContainer->push(
      $this->createNavigationCategory(Lang::get('Whatsapp::lang.whatsapp'), $navigationArray)
    );
  }
  /**
   * Gets Navigation array which with all the navigations comes under helpdesk admin panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();
    $this->injectNavigationIntoCollection($navigationArray, 'whatsapp_settings', 'fab fa-whatsapp','whatsapp/settings','whatsapp/settings');
    return $navigationArray;
  }
  private function injectNavigationIntoCollection(Collection &$navigationArray, string $name, string $iconClass, string $redirectUrl, string $routeString)
  {
    $name = Lang::get("Whatsapp::lang.$name");
    $navigationArray->push(
      $this->getNavigationObject($name, $iconClass, $redirectUrl, $routeString)
    );
  }
}