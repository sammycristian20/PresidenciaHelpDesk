<?php

namespace App\Plugins\Twitter\database\seeds\v_1_0_2;

use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Model\TwitterChannel;
use App\Plugins\Twitter\Model\TwitterHashtags;
use App\Plugins\Twitter\Model\TwitterSystemUser;
use App\Plugins\Twitter\TwitterOAuth\TwitterOAuth;
use database\seeds\DatabaseSeeder as Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->editHashtags();
        $this->addSystemTwitterUser();
        $this->fillSystemUserInTwitterChannel();
    }

    private function editHashtags()
    {
        if (TwitterHashtags::count()) {
            $hashTags = TwitterHashtags::get();
            foreach ($hashTags as $hashTag) {
                $hashTagName = $hashTag->hashtag;
                if (!Str::startsWith($hashTagName, '#')) {
                    $hashTag->hashtag = "#".$hashTagName;
                    $hashTag->save();
                }
            }
        }
    }

    private function addSystemTwitterUser()
    {
        $credentials = TwitterApp::first();

        if ($credentials && !TwitterSystemUser::count()) {
             $twitter = new TwitterOAuth(
                 $credentials->consumer_api_key,
                 $credentials->consumer_api_secret,
                 $credentials->access_token,
                 $credentials->access_token_secret
             );

             $response = $twitter->get("account/verify_credentials");
             $responseArray = json_decode(json_encode($response), true);

            if (! empty($responseArray['id_str'])) {
                TwitterSystemUser::query()->truncate();

                TwitterSystemUser::create([
                    'user_id' => $responseArray['id_str'],
                    'user_name' => $responseArray['name'],
                    'screen_name' => $responseArray['screen_name']
                ]);
            }
        }
    }

    private function fillSystemUserInTwitterChannel()
    {
        if (TwitterSystemUser::count() && TwitterChannel::count()) {
            TwitterChannel::where('channel', 'twitter')
                ->update(['system_twitter_user' => TwitterSystemUser::value('user_id')]);
        }
    }
}
