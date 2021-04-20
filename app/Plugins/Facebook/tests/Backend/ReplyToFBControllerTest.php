<?php


namespace App\Plugins\Facebook\tests\Backend;

use App\Plugins\Facebook\Controllers\ReplyToFBController;
use File;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\AddOnTestCase;

class ReplyToFBControllerTest extends AddOnTestCase
{
    private $replyController;

    public function __construct()
    {
        parent::__construct();
        $this->replyController = new ReplyToFBController();
    }

    public function testRawEncodeFileNameMethodRemovesSpecialCharsFromPassedUrlString()
    {
        $validUrl = $this->getPrivateMethod($this->replyController, 'rawEncodeFileName', ["https://fb.me/x1234*%$$.jpg"]);
        $this->assertEquals('https://fb.me/x1234%2A%25%24%24.jpg', $validUrl);
    }

    public function testGetBatchRequestElementsMethodReturnsDataInRequiredFormat()
    {
        $preparedReply = $this->getPrivateMethod(
            $this->replyController,
            'getBatchRequestElements',
            [100, null, null, "hello"]
        );

        $expected = ["messaging_type" => "UPDATE", "recipient" =>  ["id" => 100], "message" => ["text" => "hello"]];

        $this->assertEquals($expected, $preparedReply);
    }

    public function testGetInlineImagePathsMethodScansAndExtractsImageSourcesFromHtmlString()
    {
        $imageSources = $this->getPrivateMethod(
            $this->replyController,
            'getInlineImagePaths',
            ["<p>Hello <figure><img src='https://imgur.co/etxfdew.jpg'/><img src='https://pixel.com/etxfdew.jpg'/></figure></p>"]
        );

        $expected = ['https://imgur.co/etxfdew.jpg','https://pixel.com/etxfdew.jpg'];

        $this->assertEquals($expected, $imageSources);
    }
}
