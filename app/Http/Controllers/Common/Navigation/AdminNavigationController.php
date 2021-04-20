<?php

namespace App\Http\Controllers\Common\Navigation;

use App\Http\Controllers\Common\Navigation\Navigation;
use App\Http\Controllers\Controller;
use Lang;
use Illuminate\Support\Collection;
use App\Traits\NavigationHelper;
use Event;

/**
 * Handles all Admin panel navigation
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class AdminNavigationController extends Controller
{
  public function __construct()
  {
    $this->middleware('role.admin');
  }

  use NavigationHelper;

  /**
   * Injects all ticket related navigations into parent ticket navigation
   * @return Response
   */
  public function getAdminNavigation()
  {
    $navigationArray = $this->getNavigationArray();

    $navigationContainer = collect();

    $navigationContainer->push($this->createNavigationCategory(Lang::get('lang.helpdesk'), $navigationArray));

    Event::dispatch('admin-panel-navigation-data-dispatch', [&$navigationContainer]);

    return successResponse('',$navigationContainer);
  }

  /**
   * Gets Navigation array which with all the navigations comes under helpdesk admin panel
   * @return Collection
   */
  public function getNavigationArray() : Collection
  {
    $navigationArray = collect();

    $this->injectHomeNavigation($navigationArray);

    $this->injectStaffNavigation($navigationArray);

    $this->injectEmailNavigation($navigationArray);

    $this->injectManageNavigation($navigationArray);

    $this->injectTicketsNavigation($navigationArray);

    $this->injectSettingsNavigation($navigationArray);

    $this->injectAddOnsNavigation($navigationArray);

    Event::dispatch('admin-panel-navigation-array-data-dispatch', [&$navigationArray]);

    $this->injectDebugNavigation($navigationArray);

    $this->injectReturnToAgentPanelNavigation($navigationArray);

    $this->injectSignoutNavigation($navigationArray);

    return $navigationArray;
  }

  /**
   * Injects Home navigation to collection passed to it
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectHomeNavigation(Collection $navigationArray)
  {
    $navigationArray->push(
      $this->getNavigationObject(Lang::get('lang.home'), 'fas fa-home', 'admin', 'admin')
    );
  }

  /**
   * Injects Return to Agent Panel navigation to collection passed to it
   * @param  Collection &$navigationArray
   * @return null
   */
  private function injectReturnToAgentPanelNavigation(Collection $navigationArray)
  {
    $navigationArray->push(
      $this->getNavigationObject(Lang::get('lang.return_to_agent_panel'), 'fas fa-level-down-alt', 'dashboard', 'dashboard')
    );
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
   * Injects staff Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectStaffNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.staffs'));

    $navigationObject->setIconClass('fas fa-users');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject, 'agents', 'fas fa-user', 'agents', 'agents');

    $this->injectChildNavigation($navigationObject, 'departments', 'fas fa-sitemap', 'departments', 'departments');

    $this->injectChildNavigation($navigationObject, 'teams', 'fas fa-users', 'teams', 'teams');

    Event::dispatch('admin-panel-staff-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects staff Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectEmailNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.email'));

    $navigationObject->setIconClass('fas fa-envelope');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject ,'emails_settings', 'fas fa-envelope', 'emails', 'emails');

    $this->injectChildNavigation($navigationObject, 'templates', 'fas fa-file-alt', 'template-sets', 'template');

    $this->injectChildNavigation($navigationObject, 'queues', 'fas fa-upload', 'queue', 'queue');

    $this->injectChildNavigation($navigationObject, 'diagnostics', 'fas fa-plus', 'getdiagno', 'getdiagno');

    Event::dispatch('admin-panel-email-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects staff Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectManageNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.manage'));

    $navigationObject->setIconClass('fas fa-cubes');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject ,'help_topics', 'fas fa-file-alt', 'helptopic', 'helptopic');

    $this->injectChildNavigation($navigationObject, 'sla_plans', 'fas fa-clock', 'sla', 'sla');

    $this->injectChildNavigation($navigationObject, 'business_hours', 'fas fa-calendar', 'sla/business-hours/index', 'business-hours');

    $this->injectChildNavigation($navigationObject, 'form-builder', 'fas fa-file-alt', 'forms/create', 'forms/create');

    $this->injectChildNavigation($navigationObject, 'form-groups', 'fas fa-object-group', 'form-groups', 'form-groups');

    $this->injectChildNavigation($navigationObject, 'workflow', 'fas fa-sitemap', 'workflow', 'workflow');

    $this->injectChildNavigation($navigationObject, 'approval_workflow', 'fas fa-sitemap', 'approval-workflow', 'approval-workflow');

    $this->injectChildNavigation($navigationObject, 'priority', 'fas fa-asterisk', 'ticket/priority', 'priority');

    $this->injectChildNavigation($navigationObject, 'ticket_type', 'fas fa-list-ol', 'ticket-types', 'ticket-types');

    $this->injectChildNavigation($navigationObject, 'listeners', 'fas fa-magic', 'listener', 'listener');

    $this->injectChildNavigation($navigationObject, 'widgets', 'fas fa-list-alt', 'widgets', 'widgets');

    Event::dispatch('admin-panel-manage-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects Tickets Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectTicketsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.tickets'));

    $navigationObject->setIconClass('fas fa-ticket-alt');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject ,'ticket_settings', 'fas fa-file-alt', 'getticket', 'getticket');

    $this->injectChildNavigation($navigationObject, 'alert_notices', 'fas fa-bell', 'alert', 'alert');

    $this->injectChildNavigation($navigationObject, 'status', 'fas fa-plus-square', 'setting-status', 'status');

    $this->injectChildNavigation($navigationObject, 'labels', 'fab fa-lastfm', 'labels', 'labels');

    $this->injectChildNavigation($navigationObject, 'ratings', 'fas fa-star', 'getratings', 'ratings');

    $this->injectChildNavigation($navigationObject, 'close_ticket_workflow', 'fas fa-sitemap', 'close-workflow', 'close-workflow');

    $this->injectChildNavigation($navigationObject, 'tags', 'fas fa-tags', 'tag', 'tag');

    $this->injectChildNavigation($navigationObject, 'auto_assign', 'fas fa-check-square', 'auto-assign', 'auto-assign');


    $this->injectChildNavigation($navigationObject, 'source', 'fab fa-gg', 'source', 'source');

    $this->injectChildNavigation($navigationObject, 'recurring', 'fas fa-copy', 'recur/list', 'recur');

    $this->injectChildNavigation($navigationObject, 'location', 'fas fa-magic', 'helpdesk/location-types', 'location');

    Event::dispatch('admin-panel-tickets-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects Settings Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectSettingsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.settings'));

    $navigationObject->setIconClass('fas fa-cog');

    $navigationObject->setHasChildren(true);

    // $this->injectChildNavigation($navigationObject ,'dashboard-statistics', 'fas fa-tachometer-alt', 'dashboard-settings', 'dashboard-settings');

    $this->injectChildNavigation($navigationObject ,'company', 'fas fa-building', 'getcompany', 'company');

    $this->injectChildNavigation($navigationObject, 'system', 'fas fa-laptop', 'getsystem', 'getsystem');

    $this->injectChildNavigation($navigationObject, 'user-options', 'fas fa-user', 'user-options', 'user-options');

    $this->injectChildNavigation($navigationObject, 'social-login', 'fas fa-globe', 'social/media', 'social/media');

    $this->injectChildNavigation($navigationObject, 'language', 'fas fa-language', 'languages', 'languages');

    $this->injectChildNavigation($navigationObject, 'cron', 'fas fa-hourglass', 'job-scheduler', 'job-scheduler');

    $this->injectChildNavigation($navigationObject, 'security', 'fas fa-lock', 'security', 'security');

    $this->injectChildNavigation($navigationObject, 'notifications', 'fas fa-bell', 'settings-notification', 'settings-notification');

//    $this->injectChildNavigation($navigationObject, 'storage', 'fas fa-save', 'storage', 'storage');

    $this->injectChildNavigation($navigationObject, 'settings_file_system', 'fas fa-folder', 'file-system-settings', 'settings.filesystems');

    $this->injectChildNavigation($navigationObject, 'system-backup', 'fas fa-hdd', 'system-backup', 'system-backup');

    $this->injectChildNavigation($navigationObject, 'social-icon-links', 'fas fa-external-link-alt', 'widgets/social-icon', 'widgets/social-icon');

    $this->injectChildNavigation($navigationObject, 'api', 'fas fa-cogs', 'api', 'api');

    $this->injectChildNavigation($navigationObject, 'websockets', 'fas fa-bolt', 'websockets/settings', 'websockets/settings');

    $this->injectChildNavigation($navigationObject, 'webhook', 'fas fa-server', 'webhook', 'webhook');

    $this->injectChildNavigation($navigationObject, 'importer_user_import', 'fa fa-download', 'importer', 'importer.form');

    $this->injectChildNavigation($navigationObject, 'recaptcha', 'fas fa-sync-alt', 'recaptcha', 'recaptcha');

    Event::dispatch('admin-panel-settings-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }


  /**
   * Injects Add ons Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectAddOnsNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.add_ons'));

    $navigationObject->setIconClass('fas fa-puzzle-piece');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject, 'plugins', 'fas fa-plug', 'plugins', 'plugins');

    $this->injectChildNavigation($navigationObject, 'modules', 'fas fa-link', 'modules', 'modules');

    Event::dispatch('admin-panel-addons-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }

  /**
   * Injects Add ons Navigation
   * @param  array  $navigationArray
   * @return null
   */
  private function injectDebugNavigation(Collection &$navigationArray)
  {
    $navigationObject = new Navigation;

    $navigationObject->setName(Lang::get('lang.debug'));

    $navigationObject->setIconClass('fas fa-heartbeat');

    $navigationObject->setHasChildren(true);

    // injecting agent navigation
    $this->injectChildNavigation($navigationObject, 'debug-options', 'fas fa-bug', 'error-and-debugging-options', 'error-and-debugging-options');

    $this->injectChildNavigation($navigationObject, 'system-logs', 'fas fa-history', 'system-logs', 'system-logs');

    if(getActiveQueue() == 'redis'){
      $this->injectChildNavigation($navigationObject, 'queue-monitor', 'fas fa-desktop', 'horizon', 'horizon');
    }

    Event::dispatch('admin-panel-debug-navigation-data-dispatch', [&$navigationObject]);

    $navigationArray->push($navigationObject);
  }
}
