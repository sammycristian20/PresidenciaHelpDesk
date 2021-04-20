<?php

namespace App\Plugins\Ldap\database\seeds\v_2_1_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Plugins\Ldap\Model\LdapAdAttribute;
use App\Plugins\Ldap\Model\LdapFaveoAttribute;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
//      LdapAdAttribute::truncate();
//      LdapFaveoAttribute::truncate();
//      $this->LdapAdAttributeSeeder();
//      $this->LdapFaveoAttributeSeeder();
  }

  /**
   * Creates an ldap attribute
   * @param  string $faveoAttributeName
   * @param string  $ldapAttributeName
   * @param boolean $editable
   * @param boolean $overwriteable
   */
  public function setAdLdapAttribute($faveoAttributeName, $ldapAttributeName, $editable = true, $overwriteable = true)
  {
      // from seeder, it has to be moved to an array of ad attributes


    $ldapAttributeId = $this->getLdapAttributeId($ldapAttributeName);

    LdapFaveoAttribute::updateOrCreate(
      ['name'=> $faveoAttributeName],
      ['name'=>$faveoAttributeName, 'overwrite'=> false, 'mapped_to'=>$ldapAttributeId, 'editable'=>$editable, 'overwriteable'=>$overwriteable]
    );
  }

  /**
   * Creating Initial Ad attributes
   * @return null
   */
  private function LdapAdAttributeSeeder()
  {
//      LdapAdAttribute::updateOrCreate(['name'=>'FAVEO DEFAULT']);
//      LdapAdAttribute::updateOrCreate(['name'=>'samaccountname']);
//      LdapAdAttribute::updateOrCreate(['name'=>'company']);
//      LdapAdAttribute::updateOrCreate(['name'=>'contact']);
//      LdapAdAttribute::updateOrCreate(['name'=>'c']);//for country
//      LdapAdAttribute::updateOrCreate(['name'=>'department']);
//      LdapAdAttribute::updateOrCreate(['name'=>'displayname']);
//      LdapAdAttribute::updateOrCreate(['name'=>'mail']);
//      LdapAdAttribute::updateOrCreate(['name'=>'mailnickname']);
//      LdapAdAttribute::updateOrCreate(['name'=>'employeeid']);
//      LdapAdAttribute::updateOrCreate(['name'=>'employeenumber']);
//      LdapAdAttribute::updateOrCreate(['name'=>'employeetype']);
//      LdapAdAttribute::updateOrCreate(['name'=>'givenname']);
//      LdapAdAttribute::updateOrCreate(['name'=>'grouptype']);
//      LdapAdAttribute::updateOrCreate(['name'=>'homepostaladdress']);
//      LdapAdAttribute::updateOrCreate(['name'=>'initials']);
//      LdapAdAttribute::updateOrCreate(['name'=>'location']);
//      LdapAdAttribute::updateOrCreate(['name'=>'streetaddress']);
//      LdapAdAttribute::updateOrCreate(['name'=>'telephonenumber']);
//      LdapAdAttribute::updateOrCreate(['name'=>'title']);
//      LdapAdAttribute::updateOrCreate(['name'=>'userprincipalname']);
  }

  /**
   * Initial Faveo attributes
   * @return null
   */
  private function LdapFaveoAttributeSeeder()
  {
      $this->setAdLdapAttribute('user_name','FAVEO DEFAULT', false);
      $this->setAdLdapAttribute('email','FAVEO DEFAULT');
      $this->setAdLdapAttribute('first_name','FAVEO DEFAULT');
      $this->setAdLdapAttribute('last_name','FAVEO DEFAULT');
      $this->setAdLdapAttribute('phone_number','FAVEO DEFAULT');
      //$this->setAdLdapAttribute('mobile','FAVEO DEFAULT');
      $this->setAdLdapAttribute('department','FAVEO DEFAULT');
      $this->setAdLdapAttribute('organization','FAVEO DEFAULT');
      $this->setAdLdapAttribute('org_dept','FAVEO DEFAULT');
      $this->setAdLdapAttribute('role','FAVEO DEFAULT', false);
      $this->setAdLdapAttribute('import_identifier','FAVEO DEFAULT', true, false);
  }

  /**
   * gets Ad attribute ID base on  Ad attribute name
   * @param  string $attributeName
   * @return int
   */
  private function getLdapAttributeId($attributeName)
  {
      $ldapAdAttribute = LdapAdAttribute::where('name', $attributeName)->first();
      if($ldapAdAttribute){
        return $ldapAdAttribute->id;
      }
      return 0;
  }

}
