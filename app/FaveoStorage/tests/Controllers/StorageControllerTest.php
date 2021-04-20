<?php

namespace App\FaveoStorage\Tests\Controllers;

use Config;
use Storage;
use Tests\DBTestCase;
use App\Facades\Attach;
use Illuminate\Http\UploadedFile;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets as Ticket;
use App\Model\helpdesk\Ticket\Ticket_attachments;
use App\FaveoStorage\Controllers\StorageController;
use App\Model\helpdesk\Ticket\Ticket_Thread as Thread;
use App\Model\helpdesk\Ticket\Ticket_attachments as Attachment;

class StorageControllerTest extends DBTestCase
{

    private $storageController;

    public function setUp(): void
    {
        parent::setUp();

        $this->storageController = new StorageController;
    }

    public function test_upload_whenFilenameEndsWithDotTokenAndSizeIsZero_shouldNotCreateAttachment()
    {
        $fakeImage = $this->fakeImage('token');
        $ticketId = factory(Ticket::class)->create()->id;
        $threadId = factory(Thread::class)->create(['ticket_id' => $ticketId]);
        $this->storageController->upload('test.token', 'image', 0, 'attachment', $threadId, $fakeImage);
        $this->assertEquals(0, Attachment::count());
    }

    public function test_upload_whenFilenameEndsWithDotTokenAndSizeIsNotZero_shouldCreateAttachment()
    {
        $fakeImage = $this->fakeImage('token', 'test');
        $ticketId = factory(Ticket::class)->create()->id;
        $threadId = factory(Thread::class)->create(['ticket_id' => $ticketId]);
        $this->storageController->upload('test.token', '', 10, 'attachment', $threadId, $fakeImage, false, $fakeImage['filepath']);
        $this->assertEquals(1, Attachment::count());
    }

    public function test_upload_whenFilenameDoesnotEndWithDotTokenAndSizeIsZero_shouldCreateAttachment()
    {
        $fakeImage = $this->fakeImage('png', 'test');
        $ticketId = factory(Ticket::class)->create()->id;
        $threadId = factory(Thread::class)->create(['ticket_id' => $ticketId]);
        $this->storageController->upload( 'test.png', '', 0, 'attachment', $threadId, $fakeImage, false, $fakeImage['filepath']);
        $this->assertEquals(1, Attachment::count());
    }

    public function test_upload_whenFilenameContainsDotTokenButNotAtEnd_shouldCreateAttachment()
    {
        $fakeImage = $this->fakeImage('token.png', 'test');
        $ticketId = factory(Ticket::class)->create()->id;
        $threadId = factory(Thread::class)->create(['ticket_id' => $ticketId]);
        $this->storageController->upload( 'test.token.png', '', 0, 'attachment', $threadId, $fakeImage, false, $fakeImage['filepath']);
        $this->assertEquals(1, Attachment::count());
    }

    public function test_upload_whenEmptyFilesIsCopiedOrAttachedDirectlyFromCkEditor_shouldReturnFailureResponse()
    {

        $response = $this->json('post', '/img_upload', [
            'upload' => ''
        ]);

        $response->assertStatus(412)
            ->assertJsonFragment(["upload" => "This field is required"]);
    }

    public function test_upload_whenInvalidFileIsUploadedFromCkEditor_shouldReturnFailureResponse()
    {
        $fakeImage = $this->fakeImage('php', 'test');
        $response = $this->json('post', '/api/tiny-image-uploader', [
            'file' => 'test.php'
        ]);

        $response->assertStatus(412)
            ->assertJsonFragment(["file" => "The file must be an image."]);

    }

    public function test_getThumbnailUrl_whenAFilePathIsPassedWhichIsAnImage_shouldGiveAnApiEndpointToGetTheImageWithPassedHash()
    {
        $fileWhichExistsAndAnImage = public_path()."/themes/default/common/images/nodatafound.png";
        $methodResponse = $this->storageController->getThumbnailUrl($fileWhichExistsAndAnImage, "test_hash");
        $this->assertStringContainsString("test_hash", $methodResponse);
    }

    public function test_getThumbnailUrl_whenAFilePathIsPassedWhichIsTxt_shouldGivePathToAnIconWhichIsPresentInPublicFolder()
    {
        Storage::fake('system');

        $path = Attach::put('cool', UploadedFile::fake()->create('document.txt', 10), 'system');

        Storage::disk('system')->assertExists($path);

        $attachmentObject = Ticket_attachments::create(["path"=>$path, "name"=>$path, "poster"=>"ATTACHMENT", "driver" => 'system']);

        // $fileWhichExistsButNotImage = base_path()."/app/FaveoStorage/tests/FakeFiles/test.txt";
        $methodResponse = $this->storageController->getThumbnailUrl($path, "test_hash");
        
        $this->assertStringNotContainsString("test_hash", $methodResponse);
        $this->assertStringContainsString("txt.png", $methodResponse);
    }

    public function test_getThumbnailUrl_whenAFilePathWithAnUnknownExtension_shouldGivePathToNoAttachmentIcon()
    {
        Storage::fake('system');

        $path = Attach::put('cool', UploadedFile::fake()->create('document.apk', 10), 'system');

        Storage::disk('system')->assertExists($path);

        $attachmentObject = Ticket_attachments::create(["path"=>$path, "name"=>$path, "poster"=>"ATTACHMENT", "driver" => 'system']);

        $methodResponse = $this->storageController->getThumbnailUrl($path, "test_hash");
        $this->assertStringNotContainsString("test_hash", $methodResponse);
        $this->assertStringContainsString("attach.png", $methodResponse);
    }

    public function test_getThumbnailUrl_whenAFilePathWhichDoesntExist_shouldGivePathToNoDataFoundIcon()
    {
        $fileWhichDoesntExist = base_path()."/app/FaveoStorage/tests/FakeFiles/randomName.txt";
        $methodResponse = $this->storageController->getThumbnailUrl($fileWhichDoesntExist, "test_hash");
        $this->assertStringNotContainsString("test_hash", $methodResponse);
        $this->assertStringContainsString("nodatafound.png", $methodResponse);
    }

    public function test_getThumbnail_whenAnInvalidHasIsPassed_shouldGiveNoDataFoundImage()
    {
        $this->getLoggedInUserForWeb("admin");
        $methodResponse = $this->call("GET","api/thumbnail/wrong_hash");
        $this->assertEquals("nodatafound.png", $methodResponse->getFile()->getFilename());
    }

    public function test_getThumbnail_whenAnValidHashIsPassedButImageDoesntExist_shouldGiveNoDataFoundImage()
    {
        $attachmentObject = Ticket_attachments::create();
        $this->getLoggedInUserForWeb("admin");
        $methodResponse = $this->call("GET","api/thumbnail/$attachmentObject->hash_id");
        $this->assertEquals("nodatafound.png", $methodResponse->getFile()->getFilename());
    }

    public function test_getThumbnail_whenAnValidHashIsPassedAndImageExists_shouldGivenoDatFoundFallbackImageAsResponse()
    {
        $fileWhichExistsAndAnImage = public_path()."/themes/default/common/images";
        $attachmentObject = Ticket_attachments::create(["path"=>$fileWhichExistsAndAnImage, "name"=>"pdf.png", "poster"=>"inline"]);
        $this->getLoggedInUserForWeb("admin");
        $methodResponse = $this->call("GET","api/thumbnail/$attachmentObject->hash_id");
        $this->assertEquals("nodatafound.png", $methodResponse->getFile()->getFilename());
    }

    public function test_getThumbnailByPath_whenANonAuthorisedUserTriesToAccess_shouldReturn400()
    {
        $fileWhichExists = base_path()."/app/FaveoStorage/tests/FakeFiles/test.txt";

        $methodResponse = $this->call("GET","api/thumbnail-by-path", ["path"=> $fileWhichExists]);
        $methodResponse->assertStatus(400);

        $this->getLoggedInUserForWeb("user");
        $methodResponse = $this->call("GET","api/thumbnail-by-path", ["path"=> $fileWhichExists]);
        $methodResponse->assertStatus(400);
    }

    public function test_getThumbnailByPath_whenAAuthorisedUserTriesToAccess_shouldReturnImageInResponse()
    {
        $fileWhichExists = public_path()."/themes/default/common/images/pdf.png";
        $this->getLoggedInUserForWeb("admin");
        $methodResponse = $this->call("GET","api/thumbnail-by-path", ["path"=> $fileWhichExists]);
        $methodResponse->assertStatus(200);
    }

    public function test_sanitizeThreadForInlineAttachments_whenBodyIsPassedWithInvalidHashId_shouldReturnBodyAsItIs()
    {
        $body = '<img src="/api/thumbnail/invalid_hash" alt="test">';
        $thread = factory(Ticket::class)->create()->thread()->create(["body" => $body, 'ticket_id'=>1]);
        $this->storageController->sanitizeThreadForInlineAttachments($thread);
        $newBody = Ticket_Thread::whereId($thread->id)->value("body");
        $this->assertEquals($body, $newBody);

        $body = '<img src="/api/wrong_url" alt="test">';
        $thread = factory(Ticket::class)->create()->thread()->create(["body" => $body, 'ticket_id'=>1]);
        $this->storageController->sanitizeThreadForInlineAttachments($thread);
        $newBody = Ticket_Thread::whereId($thread->id)->value("body");
        $this->assertEquals($body, $newBody);
    }

    public function test_sanitizeThreadForInlineAttachments_whenBodyIsPassedWithValidHashId_shouldReturnAfterReplacingContentIdOfTargetedAttachment()
    {
        $hashId = Ticket_attachments::create(["content_id"=>"test_id"])->hash_id;
        $thumbnailUrl = Config::get("app.url")."/api/thumbnail/";
        $body = '<img src="'.$thumbnailUrl.$hashId.'" alt="test">';
        // blocking events so that method can be tested in isolation
        $thread = factory(Ticket::class)->create()->thread()->create(["body" => $body, 'ticket_id'=>1]);
        $this->storageController->sanitizeThreadForInlineAttachments($thread);
        $newBody = Ticket_Thread::whereId($thread->id)->value("body");
        $this->assertEquals('<img src="cid:test_id" alt="test">', $newBody);
    }
}
