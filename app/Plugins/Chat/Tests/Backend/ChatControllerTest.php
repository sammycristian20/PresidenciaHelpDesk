<?php

namespace App\Plugins\Chat\Tests\Backend;

use App\Plugins\Chat\Controllers\Core\ChatController;
use App\Plugins\Chat\Model\Chat;
use Tests\AddOnTestCase;

class ChatControllerTest extends AddOnTestCase
{
    public function test_tawkWebhookEntry_Blocks_InSecure_Requests()
    {
        Chat::where('short','tawk')->first()->update([
            'status' => 1,
            'secret_key' => 'hhikjskmksjsjsjskjs'
        ]);

        $response = $this->call('POST','chat/tawk/1/2',[
            "chat" => 'kjksjkxjedki'
        ]);

        $response->assertStatus(400)
        ->assertJsonFragment([
        "success"=>false,
        "message"=>"Tawk::Request is not from Tawk.to"
        ]);
    }


    public function test_checkApp_Properly_ReturnsTheAppStatus()
    {
        $cc = new ChatController;
        $x = $this->getPrivateMethod($cc,'checkApp',["tawk"]);
        $this->assertTrue(is_bool($x));
    }

    public function test_getChatsMethod_ReturnsPaginatedList_OfAllChats_WithSuccessresponse()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('GET','chat/api/chats');
        $response->assertOk()
        ->assertJsonFragment([
            "success" => true,
            "total"   => 2
        ]);
    }

    public function test_StausChangeMethod_tooglesTheStatusOfChatService_WithSuccessResponse()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('GET','chat/api/status/2');
        $response->assertOk()
        ->assertJsonFragment([
            "success"=>true,
            "message"=>trans('chat::lang.status_changed')
        ]);
    }

    public function test_perseistChatService_UpdatesChatDatabase_WithSuccessResponse()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('PUT','chat/api/update/2',[
            "department" => [
                "id" => '1',
                "name" => 'hjkdjkd'
            ],
            "helptopic" => [
                'id' => '2',
                'name' => 'wfdssffd'
            ]
        ]);
        $response->assertOK()
        ->assertJsonFragment([
            "success"=>true,
            "message"=>trans('chat::lang.updated_successfully')
        ]);
    }


    public function test_perseistChatService_PerformsValidationProperly_WithErrorResponse()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('PUT','chat/api/update/2',[]);
        $response->assertStatus(412)
        ->assertJsonFragment([
            "success"=>false,
            "message"=>[
                "department"=>"This field is required",
                "helptopic"=>"This field is required"
            ]
        ]);
    }
    
}
