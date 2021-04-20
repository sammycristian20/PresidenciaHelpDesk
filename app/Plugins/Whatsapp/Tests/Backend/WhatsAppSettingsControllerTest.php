<?php

namespace App\Plugins\Whatsapp\Tests\Backend;

use Tests\AddOnTestCase;
use App\Plugins\Whatsapp\Model\WhatsApp;

class WhatsAppSettingsControllerTest extends AddOnTestCase
{

    public function init()
    {
        WhatsApp::insert([
            "id" => rand(1,66),
            "name" => str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),
            "sid" => str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),
            "webhook_url" => "xyz.com",
            "token" => str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),
            "business_phone" => "+919008231111"
        ]);
    }

    public function test_createMethodCreatesWAAccount_AndReturnsSuccessResponse()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('POST','/whatsapp/api/create',[
            "id" => rand(1,66),
            "name" => str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),
            "sid" => str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),
            "webhook_url" => "xyz.com",
            "token" => str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),
            "business_phone" => "+919008231111"
        ]);
        $response->assertOk()->assertJsonFragment(["message"=>"Successfully Saved."]);
    }

    public function test_createMethodFailsToCreateWAAccount_AndReturnsFailureResponse()
    {
        $this->getLoggedInUserForWeb('admin');
        $response = $this->call('POST','/whatsapp/api/create',[]);
        //asserting validation messages
        $response->assertStatus(412)->assertJsonFragment([
            "sid"=>"The sid field is required.",
            "token"=>"The token field is required.",
            "business_phone"=>"The business phone field is required.",
            "name"=>"The name field is required."
        ]);
    }

    public function test_DeleteMethodDestroysWAApp_returnsSuccessResponse()
    {
        $this->getLoggedInUserForWeb('admin');
        $this->init();
        $response = $this->call('DELETE','whatsapp/api/delete/');
        $response->assertOk()->assertJsonFragment(["message"=>"App Successfully Deleted."]);
    }
}