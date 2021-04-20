<?php


namespace App\Plugins\Ldap\database\seeds\v_2_3_0;

use App\Plugins\Ldap\Controllers\ApiLdapController;
use App\Plugins\Ldap\Controllers\LdapConnector;
use App\Plugins\Ldap\Model\Ldap;
use App\Plugins\Ldap\Model\LdapAdAttribute;
use App\Plugins\Ldap\Model\LdapFaveoAttribute;
use App\Plugins\Ldap\Model\LdapSearchBase;
use database\seeds\DatabaseSeeder as Seeder;
use Illuminate\Http\Request;
use App\Model\helpdesk\Settings\CommonSettings;


class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // check whatever doesn't have a ldap_id, assign it with ldap_id
        $ldapId = Ldap::orderBy('id', 'asc')->value('id');

        LdapFaveoAttribute::where('ldap_id', null)->orWhere('ldap_id', 0)->update(['ldap_id'=> $ldapId]);

        LdapAdAttribute::where('ldap_id', null)->orWhere('ldap_id', 0)->update(['ldap_id'=> $ldapId]);

        LdapSearchBase::where('ldap_id', null)->orWhere('ldap_id', 0)->update(['ldap_id'=> $ldapId]);

        // changing ldap_unique_key to import_identifier
        LdapFaveoAttribute::where('name', 'ldap_unique_key')->update(['name'=> 'import_identifier']);

        // for ldap_only_login feature, check if ldap_login_only exists and its value is 1, if yes,
        // create an entry in common settings for the same setting
        if(\Schema::hasColumn('ldap', 'ldap_only_login')) {
            CommonSettings::updateOrCreate(['option_name'=> 'hide_default_login', 'optional_field'=>'ldap'], ['option_value'=> Ldap::value('ldap_only_login')]);
        }
    }
}
