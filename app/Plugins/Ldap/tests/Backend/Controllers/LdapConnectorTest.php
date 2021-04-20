<?php

namespace App\Plugins\Ldap\tests\Backend\Controllers;

use Tests\AddOnTestCase;
use App\Plugins\Ldap\Controllers\LdapConnector;

class LdapConnectorTest extends AddOnTestCase
{

  private $ldapConnector;

  public function setUp():void
  {
      $this->ldapConnector = new LdapConnector;
  }

  /** @group getFormattedUsername */
  public function test_getFormattedUsername_whenEscapeCharacterIsPresent()
  {
      $methodResponse = $this->getPrivateMethod($this->ldapConnector, 'getFormattedUsername', ['domain\username']);
      $this->assertEquals($methodResponse, 'username');
  }

  /** @group getFormattedUsername */
  public function test_getFormattedUsername_whenForDistinguishName()
  {
      $dn = 'cn=test,ou=faveo,ou=tk';
      $methodResponse = $this->getPrivateMethod($this->ldapConnector, 'getFormattedUsername', [$dn]);
      $this->assertEquals($methodResponse, $dn);
  }

  /** @group getFormattedUsername */
  public function test_getFormattedUsername_forNormalUsername()
  {
      $username = 'username';
      $methodResponse = $this->getPrivateMethod($this->ldapConnector, 'getFormattedUsername', [$username]);
      $this->assertEquals($methodResponse, $username);
  }
}
