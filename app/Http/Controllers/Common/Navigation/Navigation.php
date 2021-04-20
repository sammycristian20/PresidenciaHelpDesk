<?php

namespace App\Http\Controllers\Common\Navigation;

use Config;
use Illuminate\Support\Collection;

/**
 * consider that as an interface which will have these properties
 * Now if one entry has to be added to the navigation, it should have these parameters
 * This interface will be used to pass into a method which will append the reuired data into
 * the required object
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class Navigation
{
    /**
     * Name that has to be displayed on the navigation
     * @var string
     */
    public $name;

    /**
     * key of the navigation. Will not be changing with language
     * @var string
     */
    public $key;

    /**
     * Count of the navigation
     * @var int
     */
    public $count = 0;

    /**
     * If the variable has a count
     * @var bool
     */
    public $hasCount = false;

    /**
     * Class of the icon
     * @var string
     */
    public $iconClass;

    /**
     * Order of the class by which it can be sorted
     * @var int
     */
    public $order = 0;

    /**
     * The Url to which it should redirect on clicking
     * @var string
     */
    public $redirectUrl = null;

    /**
     * The string in the URL at which this navigation should be active
     * @var string
     */
    public $routeString = '';

    /**
     * If it has toggleable child
     * @var bool
     */
    public $hasChildren = false;

    /**
     * Child of the navigations, which again will be instance of Navigation
     * @var array
     */
    public $children;


    public function __construct()
    {
        $this->children = $this->children ?: collect();
    }


    /**
     * Sets name to with validation
     * @param string $value
     * @return null
     */
    public function setName(string $value)
    {
        $this->name = $value;
    }

    /**
     * Sets count to with validation
     * @param int $value
     * @return null
     */
    public function setCount($value)
    {
        $this->hasCount = true;
        $this->count = $value;
    }

    /**
     * Sets hasCount to with validation
     * @param bool $value
     * @return null
     */
    public function setHasCount(bool $value)
    {
        $this->hasCount = $value;
    }

    /**
     * Sets iconClass to with validation
     * @param bool $value
     * @return null
     */
    public function setIconClass(string $value)
    {
        $this->iconClass = $value;
    }

    /**
     * Sets order with validation
     * @param bool $value
     * @return null
     */
    public function setOrder(int $value)
    {
        $this->order = $value;
    }

    /**
     * Sets redirectUrl to with validation
     * @param string $value
     * @return null
     */
    public function setRedirectUrl(string $value)
    {
        $baseURL = Config::get('app.url');
        // check if its first character is `/`. If not then add it
        if($value[0] != '/'){
          $value = '/'.$value;
        }

        $this->redirectUrl = $baseURL.$value;
    }

    /**
     * Sets hasCount to with validation
     * @param string $value
     * @return null
     */
    public function setRouteString(string $value)
    {
        $this->routeString = $value;
    }

    /**
     * Sets hasChild as boolean with validation
     * @param  bool    $value
     * @return null
     */
    public function setHasChildren(bool $value)
    {
        $this->hasChildren = $value;
    }

    /**
     * Sets child as boolean with validation
     * @param  array  $value
     * @return null
     */
    public function setChildren(Collection $value)
    {
        // now element in each array must be an instance of Navigation class.
        // This validation check has to be put
        $this->children = $value;
    }

    /**
     * Injects child navigation into parent
     * @param  Navigation $parentNavigation
     * @param  Navigation $childNavigation
     * @return null
     */
    public function injectChildNavigation(Navigation $childNavigation)
    {
        $this->children->push($childNavigation);
    }
}
