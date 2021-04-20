<?php

namespace App\Plugins\Ldap\database\seeds\v_2_2_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Plugins\Ldap\Model\LdapAdAttribute;
use App\Plugins\Ldap\Model\LdapFaveoAttribute;
use App\Plugins\Ldap\database\seeds\v_2_1_0\DatabaseSeeder as OldDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
//      $this->addUniqueKeyToFaveoAttributes();
//      $this->addLoginableAttributesToAdAttributes();
//      $this->makeUserRoleAndUserNameEditable();
  }

  /**
   * Adding import_identifier to faveo attributes and mapping that with 'FAVEO DEFAULT'
   * @return null
   */
  private function addUniqueKeyToFaveoAttributes()
  {
    // adding unique id to
    (new OldDatabaseSeeder)->setAdLdapAttribute('import_identifier','FAVEO DEFAULT', true, false);
  }

  /**
   * adding attributes which can be used to login in LDAP
   * @return null
   */
  private function addLoginableAttributesToAdAttributes()
  {
    LdapAdAttribute::updateOrCreate(['name'=>'distinguishedname'],['name'=>'distinguishedname','is_loginable'=>true, 'is_default' => true]);
    $loginableAttributes = ['FAVEO DEFAULT','samaccountname','userprincipalname','distinguishedname', 'dn'];
    LdapAdAttribute::whereIn('name', $loginableAttributes)->update(['is_loginable'=> true]);
  }

  /**
   * Making user_name and user_role editable from advanced settings
   * @return null
   */
  private function makeUserRoleAndUserNameEditable()
  {
    //making user_name as editable
    LdapFaveoAttribute::whereIn('name',['user_name','role'])->update(['editable'=> true]);
  }

}
