<?php

  namespace App\Plugins\Ldap\tests\Backend\Controllers;

  use Tests\AddOnTestCase;
  use App\Plugins\Ldap\Controllers\LdapAdminNavigationController;

  class LdapAdminNavigationControllerTest extends AddOnTestCase
  {

      private $adminNavigation;

      public function setUp():void
      {
          parent::setUp();

          $this->adminNavigation = new LdapAdminNavigationController;
      }

      /** @group injectLdapAdminNavigation */
      public function test_injectLdapAdminNavigation_forSuccess()
      {
        $navigationContainer = collect();

        $this->adminNavigation->injectLdapAdminNavigation($navigationContainer);

        $this->assertEquals('LDAP', $navigationContainer[0]->name);
        $navigations = $navigationContainer[0]->navigations;
        $this->assertCount(1, $navigations);
        $this->assertEquals('LDAP Settings', $navigations[0]->name);
      }
  }
