<?php

  namespace App\Plugins\Facebook\tests\Backend;

  use Tests\AddOnTestCase;
  use App\Plugins\Facebook\Controllers\FacebookAdminNavigationController;

  class FacebookAdminNavigationControllerTest extends AddOnTestCase
  {

      private $adminNavigation;

      public function setUp():void
      {
          parent::setUp();

          $this->adminNavigation = new FacebookAdminNavigationController;
      }

      /** @group injectFacebookAdminNavigation */
      public function test_injectFacebookAdminNavigation_forSuccess()
      {
        $navigationContainer = collect();

        $this->adminNavigation->injectFacebookAdminNavigation($navigationContainer);

        $this->assertEquals('Facebook', $navigationContainer[0]->name);
        $navigations = $navigationContainer[0]->navigations;
        $this->assertCount(2, $navigations);
        $this->assertEquals('Page Settings', $navigations[0]->name);
        $this->assertEquals('Security Settings', $navigations[1]->name);

      }
  }
