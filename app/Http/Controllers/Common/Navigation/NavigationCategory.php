<?php

namespace App\Http\Controllers\Common\Navigation;

use Illuminate\Support\Collection;
use App\Http\Controllers\Common\Navigation\Navigation;

/**
 * Category of the navigation. For default faveo it will be helpdesk. More can be added by external plugins
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class NavigationCategory
{

  /**
   * Name of the navigation class
   * @var string
   */
  public $name;

  /**
   * Order of the navigation by which it will be displayed
   * @var int
   */
  public $order;

  /**
   * Collection of all navigations
   * @var Collection
   */
  public $navigations;


  public function __construct()
  {
      $this->navigations = $this->navigations ?: collect();
  }

  /**
   * Sets name of the navigation
   * @param string $name
   */
  public function setName(string $name)
  {
      $this->name = $name;
  }

  /**
   * Sets name of the navigation
   * @param string $name
   */
  public function setOrder(int $order)
  {
      $this->order = $order;
  }

  /**
   * Sets navigations for the class
   * @param Collection $navigations
   */
  public function setNavigations(Collection $navigations)
  {
      $this->navigations = $navigations;
  }

  /**
   * Injects navigation into Navigation class
   * @param  Navigation $navigation
   * @return null
   */
  public function injectNavigation(Navigation $navigation)
  {
      $this->navigations->push($navigation);
  }

}
