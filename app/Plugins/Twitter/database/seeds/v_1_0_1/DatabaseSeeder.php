<?php

namespace App\Plugins\Twitter\database\seeds\v_1_0_1;

use database\seeds\DatabaseSeeder as Seeder;
use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Model\TwitterHashtags;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    $tapp = TwitterApp::first();
    if($tapp) {
        TwitterHashtags::create([
            'app_id' => $tapp->id,
            'hashtag' => $tapp->hashtag_text
        ]);
    }
  }
}
