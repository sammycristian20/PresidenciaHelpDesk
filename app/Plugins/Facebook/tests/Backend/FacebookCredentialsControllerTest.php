<?php


namespace App\Plugins\Facebook\tests\Backend;

use App\Plugins\Facebook\Model\FacebookCredential;
use Tests\AddOnTestCase;

class FacebookCredentialsControllerTest extends AddOnTestCase
{
    private $createData = [
        'page_id' => 'my_page_id',
        'page_access_token' => 'my_token',
        'page_name' => 'my_page',
        'new_ticket_interval' => '10'
    ];

    public function test_createMethod_createsFacebookPageRecord_forSuccess()
    {
        $this->getLoggedInUserForWeb('admin');

        $response = $this->post(url('facebook/api/integration'), $this->createData);

        $response->assertOk()->assertJsonFragment(['message' => 'Facebook Page Successfully Added']);

        $this->assertDatabaseHas('facebook_credentials', ['page_id' => 'my_page_id']);
    }

    public function test_createMethodOnlyAcceptsUniquePageIdAndPageAccessToken_forSuccess()
    {
        $this->getLoggedInUserForWeb('admin');

        factory(FacebookCredential::class, 1)->create();

        $response = $this->post(url('facebook/api/integration'), $this->createData);

        $response->assertStatus(412)->assertJsonFragment(
            [
                "page_id" => "The page id has already been taken.",
                "page_access_token" => "The page access token has already been taken."
            ]
        );
    }

    public function test_updateMethodUpdatesRecordEvenWhenPageIDAndAcessTokenAreSame_forSuccess()
    {
        $this->getLoggedInUserForWeb('admin');

        $page = factory(FacebookCredential::class)->create(['new_ticket_interval' => 15])->id;

        //before update
        $this->assertDatabaseHas('facebook_credentials', ['new_ticket_interval' => 15]);

        $response = $this->put(url("facebook/api/integration/$page"), $this->createData);

        $response->assertOk()->assertJsonFragment(['message' => 'Facebook Page Information Updated Successfully']);

        $this->assertDatabaseHas('facebook_credentials', ['new_ticket_interval' => 10]);
    }

    public function test_updateMethodfailsOnUnknownFacebookPageResourceId()
    {
        $this->getLoggedInUserForWeb('admin');

        factory(FacebookCredential::class)->create();

        $response = $this->put(url("facebook/api/integration/100099999777777"), $this->createData);

        $response->assertStatus(400)->assertJsonFragment(
            ['message' => 'No Such Facebook Page Found']
        );
    }

    public function test_changeStatusChangesStatusOfFacebookPage_ForSuccess()
    {
        $this->getLoggedInUserForWeb('admin');

        $page = factory(FacebookCredential::class)->create()->page_id;

        $response = $this->get("facebook/api/integration/status/$page");

        $response->assertOk()->assertJsonFragment(
            ['message' => 'Facebook Page Status Changed Successfully']
        );

        $this->assertDatabaseHas('facebook_credentials', ['active' => 0]);
    }

    public function test_indexMethodsReturnsAllFacebookPages_forSuccess()
    {
        $this->getLoggedInUserForWeb('admin');

        factory(FacebookCredential::class)->create();

        $response = $this->get(url('facebook/api/integration'));

        $response->assertOk()->assertJsonFragment(
            ['total' => 1,'page_name' => 'my_page']
        );
    }

    public function test_destroyMethodDeletesFacebookPageResource_ForSuccess()
    {
        $this->getLoggedInUserForWeb('admin');

        $page = factory(FacebookCredential::class)->create()->id;

        $response = $this->delete(url("facebook/api/integration/$page"));

        $response->assertOk()->assertJsonFragment(
            ["message" => "Facebook Page Deleted Successfully"]
        );
    }

    public function test_nonAdminsCantAccessFacebookRelatedPages()
    {
        $this->getLoggedInUserForWeb();

        $this->get(url('facebook/settings'))->assertStatus(302);

        $this->get(url('facebook/integration/create'))->assertStatus(302);
    }
}

