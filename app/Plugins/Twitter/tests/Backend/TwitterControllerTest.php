<?php

namespace App\Plugins\Twitter\tests\Backend;

use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Plugins\Twitter\Handler\TwitterAPIHandler;
use Tests\AddOnTestCase;
use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Controllers\Twitter;
use App\Plugins\Twitter\Controllers\TwitterController;
use App\Plugins\Twitter\Model\TwitterChannel;
use App\Plugins\Twitter\Model\TwitterHashtags;

class TwitterControllerTest extends AddOnTestCase
{

    public function test_CreateApp_Returns_ErrorJsonResponse_WhenTriedToCreateApp_WithFakeTokens()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('POST','/twitter/api/create',[
            'consumer_api_secret' => 'hgyffddgf',
            'consumer_api_key'    => 'ndkjhhdln',
            'access_token'        => 'rdfefres',
            'access_token_secret' => 'rjdojdj',
            'hashtag_text'        => ['dkdddkmfjfjdkjdjfe']
        ]);

        $response->assertStatus(400);
    }

    public function test_deleteApp_deletesTheApp_returnsSuccessResponse()
    {
        $this->getLoggedInUserForWeb('admin');
        $appId = factory(TwitterApp::class)->create()->id;
        TwitterHashtags::create([
            'app_id' => $appId,
            'hashtag' => 'hashtags'
        ]);
        $response = $this->call('DELETE','/twitter/api/delete/'.$appId);

        $response->assertOk()->assertJsonFragment([
            'message' => 'Twitter App successfully deleted.',
        ]);
        $this->assertDatabaseMissing('twitter_hashtags',[
            'hashtag' => 'hashtags'
        ]);
    }

    public function test_deleteApp_shouldRedirect_whenNonAdminTries_toDelete()
    {
        $this->getLoggedInUserForWeb('agent');
        $appId = factory(TwitterApp::class)->create()->id;
        TwitterHashtags::create([
            'app_id' => $appId,
            'hashtag' => 'hashtags'
        ]);
        $response = $this->call('DELETE','/twitter/api/delete/'.$appId);

        $response->assertStatus(302);
    }
}
