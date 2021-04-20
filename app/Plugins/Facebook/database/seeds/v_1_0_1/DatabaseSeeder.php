<?php

namespace App\Plugins\Facebook\database\seeds\v_1_0_1;

use App\Plugins\Facebook\Model\FacebookPages;
use App\Plugins\Facebook\Model\FbChannel;
use database\seeds\DatabaseSeeder as Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->seedForPageId();
    }

    /**
     * Since change in FB Policy pageAccessToken is variable in nature
     * So Added PageId as of Plugin v1_0_1 so seeding existing message store with pageid
     * @return void
     */
    private function seedForPageId() :void
    {
        $pageInformation = FacebookPages::all(['page_id','access_token']);
        if($pageInformation) {
            foreach ($pageInformation->toArray() as $page) {
                FbChannel::where('page_access_token',$page['access_token'])->update(['page_id'=>$page['page_id']]);
            }
        }
    }
}
