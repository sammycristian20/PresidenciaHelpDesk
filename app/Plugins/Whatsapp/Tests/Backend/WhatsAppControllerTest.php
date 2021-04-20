<?php

namespace App\Plugins\Whatsapp\Tests\Backend;

use App\Plugins\Whatsapp\Controllers\WhatsappController;
use App\Plugins\Whatsapp\Model\WhatsApp;
use App\Plugins\Whatsapp\Model\WhatsappWebhookStore;
use Tests\AddOnTestCase;

class WhatsAppControllerTest extends AddOnTestCase
{
    private $wa;

    public function init()
    {
        WhatsApp::insert([
            "id" => rand(1,199),
            "name" => str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),
            "sid" => str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),
            "webhook_url" => "xyz.com",
            "token" => str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),
            'is_image_inline' => 1,
            'new_ticket_interval' => 1
        ]);
    }

    public function webhookStoreInit()
    {
        WhatsappWebhookStore::insert([
            'id' => rand(1,55),
            'MediaContentType0' => 'ss',
            'SmsMessageSid' => 'jdfrhef',
            'NumMedia' => 0,
            'From' => '+919008248379',
            'Body' => 'Hello',
        ]);
    }


    public function test_Webhook_VerifiesFakeRequests_AndReturnsFailureResponse()
    {
        $this->init();
        $response = $this->call('POST','/whatsapp',[
            "from" => "4545556666",
            "Body" => "This is Hacker.."
        ]);

        $response->assertStatus(400)
                 ->assertJsonFragment([
                     "message" => "Someone is trying to sniff requests from Twilio "
        ]);

        $this->assertDatabaseMissing('whatsapp_webhook_store', ["Body" => "This is Hacker.."]);
    }

    public function test_isAttachmentNotInlineMethod_Correctly_Determines_ImageAttachmentType()
    {
        $this->init();
        $wa = new WhatsappController;
        $x = $this->getPrivateMethod($wa,'isThisAttachmentNotInline',["image/png"]);
        //false means image is inline.
        $this->assertFalse($x);
    }


    public function test_isAttachmentNotInlineMethod_Correctly_Determines_NonImageAttachmentType()
    {
        $this->init();
        $wa = new WhatsappController;
        $x = $this->getPrivateMethod($wa,'isThisAttachmentNotInline',["video/mp4"]);
        //true means image not inline.
        $this->assertTrue($x);
    }

    public function test_generateTicketMitheodMethod_Saves_WhatsappMessageDetails_InDB()
    {
        $this->init();
        $wa = new WhatsappController;
        $this->getPrivateMethod($wa,'generateTicket',["Hello","+919008248379"]);
        $this->assertDatabaseHas('whatsapp_messages',['from' => '+919008248379']);
        $this->assertDatabaseHas('ticket_thread',['title' => "Message from WhatsApp"]);
    }

    public function test_WebhookProcessMethodProcesses_WhatsappPayload_And_FlushDB()
    {
        $wa = new WhatsappController;
        $this->webhookStoreInit();
        $this->init();
        $this->assertDatabaseHas('whatsapp_webhook_store',['from' => '+919008248379']);
        $this->getPrivateMethod($wa,'webhookProcess',[]);
        $this->assertDatabaseHas('whatsapp_messages',['from' => '+919008248379']);
        $this->assertDatabaseHas('ticket_thread',['title' => "Message from WhatsApp"]);
        $this->assertDatabaseMissing('whatsapp_webhook_store',['from' => '+919008248379']);
    }

}