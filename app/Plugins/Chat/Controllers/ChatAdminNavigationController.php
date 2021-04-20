<?php
namespace App\Plugins\Chat\Controllers;
use Illuminate\Support\Collection;
use Lang;
use App\Traits\NavigationHelper;
/**
 * Handles Admin Navigation for Twitter plugin
 */
class ChatAdminNavigationController
{
  use NavigationHelper;
  /**
   * Injects Twitter specific navigation to core admin navigation
   * @param Collection $coreNavigationArray
   * @return null
   */
  public function injectChatAdminNavigation(Collection &$coreNavigationContainer)
  {
    $navigationArray = $this->getNavigationArray();
    $coreNavigationContainer->push(
      $this->createNavigationCategory(Lang::get('chat::lang.chat'), $navigationArray)
    );
  }
  /**
   * Gets Navigation array which with all the navigations comes under helpdesk admin panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();
    $this->injectNavigationIntoCollection($navigationArray, 'chat_settings', 'fas fa-comments','chat/settings','chat/settings');
    return $navigationArray;
  }
  private function injectNavigationIntoCollection(Collection &$navigationArray, string $name, string $iconClass, string $redirectUrl, string $routeString)
  {
    $name = Lang::get("chat::lang.$name");
    $navigationArray->push(
      $this->getNavigationObject($name, $iconClass, $redirectUrl, $routeString)
    );
  }
}