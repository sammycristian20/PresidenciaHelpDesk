<?php

namespace App\Http\Controllers\Common\Navigation;

use App\Http\Controllers\Common\Navigation\Navigation;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketsCategoryController;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use Lang;
use DB;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketFilterController;
use App\Model\helpdesk\Ticket\TicketFilter;
use App\User;

/**
 * Handles all ticket related Navigations. It is kept as a seperate class so that
 * filters can be added to it in later stage of project
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class TicketNavigationController
{
  /**
   * Instance of TicketListController which will be used to get the count of the tickets
   * @var TicketListController
   */
  private $ticketListController;

  public function __construct(TicketListController $ticketListController)
  {
      $this->ticketListController = $ticketListController;
  }

  /**
   * injects navigation elements required by inbox
   * @param Navigation $ticketNavigation
   * @return null
   */
  private function injectInboxNavigation(Navigation &$ticketNavigation)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.inbox'));

    $navigationObject->setCount($this->getCount(['category'=>'inbox']));

    $navigationObject->setHasCount(true);

    $navigationObject->setIconClass('fas fa-inbox');

    $navigationObject->setRedirectUrl('tickets?show%5B%5D=inbox&departments%5B%5D=All');

    $navigationObject->setRouteString('inbox');

    $ticketNavigation->injectChildNavigation($navigationObject);
  }

  /**
   * injects navigation elements required by inbox
   * @param Navigation $ticketNavigation
   * @return null
   */
  private function injectMyTicketNavigation(Navigation &$ticketNavigation)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.my_tickets'));

    $navigationObject->setCount($this->getCount(['category'=>'mytickets']));

    $navigationObject->setHasCount(true);

    $navigationObject->setIconClass('fas fa-user');

    $navigationObject->setRedirectUrl('tickets?show%5B%5D=mytickets&departments%5B%5D=All');

    $navigationObject->setRouteString('mytickets');

    $ticketNavigation->injectChildNavigation($navigationObject);
  }

  /**
   * Gets navigation elements required by unassigned
   * @param Navigation $ticketNavigation
   * @return null
   */
  private function injectUnassignedNavigation(Navigation &$ticketNavigation)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.unassigned'));

    $navigationObject->setCount($this->getCount(['category'=>'unassigned']));

    $navigationObject->setHasCount(true);

    $navigationObject->setIconClass('fas fa-user-times');

    $navigationObject->setRedirectUrl('tickets?show%5B%5D=unassigned&departments%5B%5D=All');

    $navigationObject->setRouteString('unassigned');

    $ticketNavigation->injectChildNavigation($navigationObject);
  }

  /**
   * Gets navigation elements required by overdue
   * @param Navigation $ticketNavigation
   * @return null
   */
  private function injectOverdueNavigation(Navigation &$ticketNavigation)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.overdue'));

    $navigationObject->setCount($this->getCount(['category'=>'overdue']));

    $navigationObject->setHasCount(true);

    $navigationObject->setIconClass('fas fa-calendar-times');

    $navigationObject->setRedirectUrl('tickets?show%5B%5D=overdue&departments%5B%5D=All');

    $navigationObject->setRouteString('overdue');

    $ticketNavigation->injectChildNavigation($navigationObject);
  }

  /**
   * Gets navigation elements required by unapproved
   * @param Navigation $ticketNavigation
   * @return null
   */
  private function injectUnapprovedNavigation(Navigation &$ticketNavigation)
  {
    if($this->shallUnapprovedNavigationBeVisible()){

      $navigationObject = new Navigation;

      $navigationObject->setName(Lang::get('lang.unapproved'));

      $navigationObject->setCount($this->getCount(['category'=>'unapproved']));

      $navigationObject->setHasCount(true);

      $navigationObject->setIconClass('fas fa-clock');

      $navigationObject->setRedirectUrl('tickets?show%5B%5D=unapproved&departments%5B%5D=All');

      $navigationObject->setRouteString('unapproved');

      $ticketNavigation->injectChildNavigation($navigationObject);
    }
  }

  /**
   * Gets navigation elements required by waiting for my approval
   * @param Navigation $ticketNavigation
   * @return null
   */
  private function injectWaitingForMyApprovalNavigation(Navigation &$ticketNavigation)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.waiting-for-approval'));

    $navigationObject->setCount($this->getCount(['category'=>'waiting-for-approval']));

    $navigationObject->setHasCount(true);

    $navigationObject->setIconClass('fas fa-clock');

    $navigationObject->setRedirectUrl('tickets?show%5B%5D=waiting-for-approval&departments%5B%5D=All');

    $navigationObject->setRouteString('waiting-for-approval');

    $ticketNavigation->injectChildNavigation($navigationObject);
  }

  /**
   * Gets navigation elements required by closed
   * @param Navigation $ticketNavigation
   * @return null
   */
  private function injectClosedNavigation(Navigation &$ticketNavigation)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.closed'));

    $navigationObject->setCount($this->getCount(['category'=>'closed']));

    $navigationObject->setHasCount(true);

    $navigationObject->setIconClass('fas fa-minus-circle');

    $navigationObject->setRedirectUrl('tickets?show%5B%5D=closed&departments%5B%5D=All');

    $navigationObject->setRouteString('closed');

    $ticketNavigation->injectChildNavigation($navigationObject);
  }

  /**
   * Gets navigation elements required by trash
   * @param Navigation $ticketNavigation
   * @return null
   */
  private function injectTrashNavigation(Navigation &$ticketNavigation)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.trash'));

    $navigationObject->setCount($this->getCount(['category'=>'deleted']));

    $navigationObject->setHasCount(true);

    $navigationObject->setIconClass('fas fa-trash');

    $navigationObject->setRedirectUrl('tickets?show%5B%5D=trash&departments%5B%5D=All');

    $navigationObject->setRouteString('trash');

    $ticketNavigation->injectChildNavigation($navigationObject);
  }

  /**
   * Gets navigation elements required by spam
   * @param Navigation $ticketNavigation
   * @return null
   */
  private function injectSpamNavigation(Navigation &$ticketNavigation)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.spam'));

    $navigationObject->setCount($this->getCount(['category'=>'spam']));

    $navigationObject->setHasCount(true);

    $navigationObject->setIconClass('fas fa-exclamation-triangle');

    $navigationObject->setRedirectUrl('tickets?show%5B%5D=spam&departments%5B%5D=All');

    $navigationObject->setRouteString('spam');

    $ticketNavigation->injectChildNavigation($navigationObject);
  }

  /**
   * Gets create ticket Navigation
   * @param Navigation $ticketNavigation
   * @return null
   */
  public function injectCreateTicketNavigation(Navigation &$ticketNavigation)
  {
    if($this->shallCreateTicketBeVisible()){

      $navigationObject = new Navigation;

      $navigationObject->setName(Lang::get('lang.create_ticket'));

      $navigationObject->setHasCount(false);

      $navigationObject->setIconClass('fas fa-ticket-alt');

      $navigationObject->setRedirectUrl('newticket');

      $navigationObject->setRouteString('newticket');

      $navigationObject->setHasChildren(false);

      $ticketNavigation->injectChildNavigation($navigationObject);
    }
  }

  /**
   * Injects all ticket related navigations into parent ticket navigation
   * @param  Navigation &$ticketNavigation  parent navigation object
   * @return null
   */
  public function injectTicketNavigation(Navigation &$ticketNavigation)
  {
    $this->injectCreateTicketNavigation($ticketNavigation);

    $this->injectInboxNavigation($ticketNavigation);

    $this->injectMyTicketNavigation($ticketNavigation);

    $this->injectUnassignedNavigation($ticketNavigation);

    $this->injectOverdueNavigation($ticketNavigation);

    $this->injectUnapprovedNavigation($ticketNavigation);

    $this->injectWaitingForMyApprovalNavigation($ticketNavigation);

    $this->injectClosedNavigation($ticketNavigation);

    $this->injectTrashNavigation($ticketNavigation);

    $this->injectSpamNavigation($ticketNavigation);

    // // injecting Filters in ticket navigation
    $this->injectFilterNavigation($ticketNavigation);

  }

  /**
   * If create ticket bar should be visible
   * @return bool
   */
  private function shallCreateTicketBeVisible()
  {
    return User::has('create_ticket');
  }

  /**
   * If unapproved status bar should be visible
   * @return bool
   */
  private function shallUnapprovedNavigationBeVisible()
  {
    return User::has('view_unapproved_tickets');
  }

  /**
   * Gets navigation elements required by Filter
   * @param Navigation $ticketNavigation
   * @return void
   */
  private function injectFilterNavigation(Navigation &$ticketNavigation)
  {
    $ticketFilterObject = new TicketFilterController;

    $filters = json_decode($ticketFilterObject->index()->content())->data;

    if(count($filters->own)) {
      $this->injectFilterNavigationByLabel($filters->own, $ticketNavigation, 'my_filters');
    }

    if(count($filters->shared)) {
      $this->injectFilterNavigationByLabel($filters->shared, $ticketNavigation, 'shared_filters');
    }
  }

  /**
   * Injects filter navigation by its label
   * @param  [type]     $filters
   * @param  Navigation $ticketNavigation
   * @param  string     $label
   * @return null
   */
  private function injectFilterNavigationByLabel($filters, Navigation &$ticketNavigation, string $label)
  {
    $navigationObject = new Navigation;
    $navigationObject->setName(Lang::get("lang.$label"));
    $navigationObject->setHasCount(false);
    $navigationObject->setIconClass('fas fa-filter');
    $navigationObject->setHasChildren(true);
    foreach ($filters as $filter) {
      $navigationObject->injectChildNavigation($this->getFilterNavigation($filter));
    }
    $ticketNavigation->injectChildNavigation($navigationObject);
  }

  /**
   * Gets navigation elements required by specific filter
   * @param  $filter
   * @return $navigationObject
   */
  private function getFilterNavigation($filter)
  {
    $navigationObject = new Navigation;
    $navigationObject->setName(ucwords($filter->name));
    $navigationObject->setHasCount(true);

    $navigationObject->setCount($this->getTicketCountForCustomFilter($filter->id));
    $navigationObject->setRedirectUrl("tickets/filter/$filter->id");
    $navigationObject->setRouteString("filter/$filter->id");
    $navigationObject->setIconClass($filter->icon_class);
    $navigationObject->setHasChildren(false);
    return $navigationObject;
  }

  /**
   * Gets filter's ticket count by accepting it id
   * will be appended in the navigation count
   * @return
   */
  private function getTicketCountForCustomFilter($filterId)
  {
    $parameters = TicketFilter::getFilterParametersByFilterId($filterId);

    return $this->getCount($parameters);
  }

  /**
   * Gets ticket count based on filter parameters
   * @param  array $parameters associative array of keys and values of filter
   * @return int
   */
  private function getCount($parameters) : int
  {
    return $this->ticketListController->getTicketCountByParameters($parameters);
  }
}
