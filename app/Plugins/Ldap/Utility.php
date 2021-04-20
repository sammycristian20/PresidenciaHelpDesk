<?php

namespace App\Plugins\Ldap;

use Schema;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Artisan;
use App\Plugins\Ldap\Model\Ldap;
use App\Plugins\Ldap\Model\LdapAdAttribute;
use App\Plugins\Ldap\Model\LdapFaveoAttribute;
use App\Model\MailJob\Condition;

/**
 * this entire class is useless and must be removed once testing for Ldap is done
 */
class Utility {

    /**
     * runs migration required for ldap
     * @return null
     */
    public function configure()
    {
        try {
            //running required migrations
            Artisan::call('migrate', ['--path' => 'app/Plugins/Ldap/database/migrations','--force'=>true]);

            $checkValue =  Condition::where('job','ldap')->value('value');
            if(!$checkValue) {
                Condition::create([
                    "job"=>"ldap", "value"=>"everyTenMinutes",
                    "icon" => "fa fa-cloud-download", "command" => "ldap:sync",
                    "job_info" => "ldap-info", "plugin_job" => 1,
                    "plugin_name" => "Ldap"
                ]);
            }
            // inputting one entry
            if(!Ldap::count()){
                Ldap::create();
            }

            // TODO: create an installer for plugins which handles this instead of querying it
            // OR observe plugin table, if it gets an entry
            // PROBELM: as soon as any entry happens in plugin table,
            // the service provider is not registered so calling this will not be possible
            // so either register the service provider after the entry(which might affect how other
            // plugins are working) or configure it in when the first time service provider is hit
            // and store that information in cache
            if(!LdapAdAttribute::count()){
                $this->seed();
            }

        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function seed(){
      LdapAdAttribute::truncate();
      LdapFaveoAttribute::truncate();
      $this->LdapAdAttributeSeeder();
      $this->LdapFaveoAttributeSeeder();
    }

    private function LdapAdAttributeSeeder()
    {
        LdapAdAttribute::create(['name'=>'FAVEO DEFAULT']);
        LdapAdAttribute::create(['name'=>'samaccountname']);
        LdapAdAttribute::create(['name'=>'company']);
        LdapAdAttribute::create(['name'=>'contact']);
        LdapAdAttribute::create(['name'=>'c']);//for country
        LdapAdAttribute::create(['name'=>'department']);
        LdapAdAttribute::create(['name'=>'displayname']);
        LdapAdAttribute::create(['name'=>'mail']);
        LdapAdAttribute::create(['name'=>'mailnickname']);
        LdapAdAttribute::create(['name'=>'employeeid']);
        LdapAdAttribute::create(['name'=>'employeenumber']);
        LdapAdAttribute::create(['name'=>'employeetype']);
        LdapAdAttribute::create(['name'=>'givenname']);
        LdapAdAttribute::create(['name'=>'grouptype']);
        LdapAdAttribute::create(['name'=>'homepostaladdress']);
        LdapAdAttribute::create(['name'=>'initials']);
        LdapAdAttribute::create(['name'=>'location']);
        LdapAdAttribute::create(['name'=>'streetaddress']);
        LdapAdAttribute::create(['name'=>'telephonenumber']);
        LdapAdAttribute::create(['name'=>'title']);
        LdapAdAttribute::create(['name'=>'userprincipalname']);
    }

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
    }

    private function getLdapAttributeId($attributeName)
    {
        $ldapAdAttribute = LdapAdAttribute::where('name', $attributeName)->first();
        if($ldapAdAttribute){
          return $ldapAdAttribute->id;
        }
        return 0;
    }

    private function setAdLdapAttribute($faveoAttributeName, $ldapAttributeName, $editable = true)
    {
      $ldapAttributeId = $this->getLdapAttributeId($ldapAttributeName);

      LdapFaveoAttribute::create(
        ['name'=>$faveoAttributeName, 'overwrite'=> false, 'mapped_to'=>$ldapAttributeId, 'editable'=>$editable]
      );
    }
}
