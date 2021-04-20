<?php
namespace App\Plugins\Twitter\tests\Backend;

use App\Plugins\Twitter\Controllers\TwitterAdminNavigationController;

use Tests\AddOnTestCase;

class TwitterAdminNavigationControllerTest extends AddOnTestCase
{

    private $adminNavigation;

    public function setUp():void
    {
        parent::setUp();

        $this->adminNavigation = new TwitterAdminNavigationController;
    }

    /** @group injectTwitterAdminNavigation */
    public function test_injectTwitterAdminNavigation_forSuccess()
    {
    $navigationContainer = collect();

    $this->adminNavigation->injectTwitterAdminNavigation($navigationContainer);

    $this->assertEquals('Twitter', $navigationContainer[0]->name);
    $navigations = $navigationContainer[0]->navigations;
    $this->assertCount(1, $navigations);
    $this->assertEquals('Twitter Settings', $navigations[0]->name);
    }
}
