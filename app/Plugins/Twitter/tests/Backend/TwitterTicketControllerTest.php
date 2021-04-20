<?php

namespace App\Plugins\Twitter\tests\Backend;

use App\Plugins\Twitter\Controllers\TwitterTicketController;
use App\Plugins\Twitter\Model\TwitterApp;
use App\Plugins\Twitter\Model\TwitterChannel;
use Tests\AddOnTestCase;

class TwitterTicketControllerTest extends AddOnTestCase
{
    private $entityField = [
        "channel" => "twitter",
        "name" => "Praj",
        "user_id" => "123",
        "username" => "PrajNu",
        "email" => "N/A",
        "posted_at" => '2019-04-03',
        "message_id" => "MSG1076",
        "via" => "message",
        "body" => 'Dummy'
    ];

    public function test_CreateTicketMethod_CreatesTicket()
    {
        $fields = [
            $this->entityField
        ];
        $expected = ['message_id' => "MSG1076",'username' => 'PrajNu'];
        factory(TwitterApp::class)->create();
        $twitterTicketControllerObject = new TwitterTicketController();
        $this->assertDatabaseMissing('twitter_channel', $expected);
        $this->getPrivateMethod($twitterTicketControllerObject, 'createTicket', [$fields]);
        $this->assertDatabaseHas('twitter_channel', $expected);
        $this->assertDatabaseHas('ticket_thread', ['title' => "Message from Twitter"]);
    }

    public function test_entityExistsMethod_ChecksWhether_MessageAlreadyExists()
    {
        factory(TwitterChannel::class)->create();
        $twitterTicketControllerObject = new TwitterTicketController();
        $x = $this->getPrivateMethod($twitterTicketControllerObject, 'entityExists', ["1198529298426028032","tweet"]);
        $this->assertTrue($x);
    }

    public function test_getTicketDetailsIfThisEntityIsReplyToAnExistingTicketMethod_ChecksWhetherTheNextMessageIsReplyOrNot()
    {
        factory(TwitterApp::class)->create();
        $twitterEntry = factory(TwitterChannel::class)->create();
        $twitterTicketControllerObject = new TwitterTicketController();
        $x = $this->getPrivateMethod(
            $twitterTicketControllerObject,
            'getTicketDetailsIfThisEntityIsReplyToAnExistingTicket',
            [["user_id" => "8959u7","via" =>"tweet",'posted_at' =>'2019-11-19 14:40:26']]
        );
        $this->assertEquals(['ticket_id' => $twitterEntry->ticket_id, 'user_id' => null], $x);
    }
}
