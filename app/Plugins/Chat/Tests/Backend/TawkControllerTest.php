<?php

namespace App\Plugins\Chat\Tests\Backend;

use App\Plugins\Chat\Controllers\Tawk\TawkController;
use App\Plugins\Chat\Model\Chat;
use Tests\AddOnTestCase;

class TawkControllerTest extends AddOnTestCase
{
    private function setPrivateProprty($classObj,$privateProperty,$value)
    {
        $reflection = new \ReflectionClass($classObj);
        $property = $reflection->getProperty($privateProperty);
        $property->setAccessible(true);
        $property->setValue($classObj, $value);
    }


    public function test_generateTicketMethod_Creates_Ticket_Successfully()
    {
        $tc = new TawkController([],1,1);
        $this->setPrivateProperty($tc,'email','abc@yahoo.com');
        $this->setPrivateProperty($tc,'body','Message from Kwat');
        $this->setPrivateProperty($tc,'subject','Message Subject');
        $result = $this->getPrivateMethod($tc,'generateTicket',[]);
        $this->assertEqualsCanonicalizing($result, ["AAAA-0000-0000",true]);
        $this->assertDatabaseHas('tickets', ['ticket_number' => "AAAA-0000-0000"]);
    }


    public function test_persistChat_Saves_TawkMessage_InDB()
    {
        $tc = new TawkController([],1,1);
        $this->setPrivateProperty($tc,'request',collect(['chatId' => 'hjsjshk','message' => ['text' => 'hello']]));
        $this->getPrivateMethod($tc,'persistChat',[]);
        $this->assertDatabaseHas('tawk_chats', ['chat_id' => "hjsjshk",'body' => 'hello']);
    }


    public function test_checkUser_ProperlyChecksWhetherTheUser_WithTheSuppliedEmail()
    {
        $tc = new TawkController([],1,1);
        $result = $this->getPrivateMethod($tc,'checkUser',['abc@yahoo.com']);
        $this->assertEquals('',$result);
    }

    public function test_getTawkSecret_GetsTheTawkSecretKey()
    {
        Chat::where('short','tawk')->first()->update([
            'secret_key' => 'hhikjskmksjsjsjskjs'
        ]);
        $tc = new TawkController([],1,1);
        $result = $this->getPrivateMethod($tc,'getTawkSecret',[]);
        $this->assertEquals('hhikjskmksjsjsjskjs',$result);
    }
}