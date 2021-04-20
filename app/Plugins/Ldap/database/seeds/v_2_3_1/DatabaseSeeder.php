<?php

namespace App\Plugins\Ldap\database\seeds\v_2_3_1;

use App\Plugins\Ldap\Model\Ldap;
use App\Plugins\Ldap\Model\LdapAdAttribute;
use App\Plugins\Ldap\Model\LdapFaveoAttribute;

class DatabaseSeeder extends \database\seeds\DatabaseSeeder
{
    public function run()
    {
        $this->seedLocationRelatedEntriesForExistingActiveDirectories();
    }

    private function seedLocationRelatedEntriesForExistingActiveDirectories()
    {
        $activeDirectories = Ldap::get();

        foreach ($activeDirectories as $activeDirectory) {
            $faveoDefaultAdAttributeId = LdapAdAttribute::where(['name' => 'FAVEO DEFAULT','ldap_id' => $activeDirectory->id])->value('id');

            if (! LdapFaveoAttribute::where(['name' => 'location','mapped_to' =>$faveoDefaultAdAttributeId, 'ldap_id' => $activeDirectory->id])->count()) {
                $activeDirectory->faveoAttributes()
                    ->saveMany([new LdapFaveoAttribute(['mapped_to' => $faveoDefaultAdAttributeId,'name' => 'location'])]);
            }

            if (!LdapAdAttribute::where(['name' => 'l', 'ldap_id' => $activeDirectory->id])->count()) {
                $activeDirectory->adAttributes()
                    ->saveMany([new LdapAdAttribute(['name' => 'l'])]);
            }
        }
    }
}
