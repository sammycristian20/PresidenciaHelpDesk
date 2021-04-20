<?php

namespace App\Bill\Controllers;

use Illuminate\Support\Collection;
use App\Traits\NavigationHelper;
use App\Http\Controllers\Common\Navigation\Navigation;
/**
 * Handles Admin Navigation for service desk
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class BillAdminNavigationController
{
  use NavigationHelper;

    /**
     * Injects service desk specific navigation to core agent navigation
     * @param Collection $coreNavigationArray
     * @return null
     */
    public function injectBillAdminNavigation(Collection &$coreNavigationContainer)
    {
        $navigationObject = new Navigation;

        $navigationObject->setName(trans('Bill::lang.bill'));

        $navigationObject->setIconClass('fas fa-dollar-sign');

        $navigationObject->setHasChildren(true);

        $this->injectChildNavigation($navigationObject, 'options', 'fas fa-wrench', 'bill', 'bill');
        $this->injectChildNavigation($navigationObject, 'package', 'fas fa-suitcase', 'bill/package/inbox', 'bill/package/inbox');
        $this->injectChildNavigation($navigationObject, 'payment_gateway', 'fas fa-money-bill-alt', 'bill/payment-gateways', 'bill/payment-gateways');

        $coreNavigationContainer->push(
            $navigationObject
        );
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
        $name = trans("Bill::lang.$name");
        $parentNavigation->injectChildNavigation($this->getNavigationObject($name, $iconClass, $redirectUrl, $routeString));
    }
}
