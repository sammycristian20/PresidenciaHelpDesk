<?php

namespace App\FileManager\Tests;

use Illuminate\Http\UploadedFile;
use Storage;
use Tests\DBTestCase;

class FileManagerControllerTest extends DBTestCase
{
    private function uploadFile($filename)
    {
        return $this->post(route('fm.upload'), [
            'disk' => 'private',
            'path' => null,
            'overwrite' => 0,
            'files' => [
                UploadedFile::fake()->image($filename)
            ]
        ]);
    }

    public function test_uploadMethod_uploadsTheFile_successfully()
    {
        $this->getLoggedInUserForWeb('agent');

        Storage::fake('private');

        $this->uploadFile('photo1.jpg')->assertOk();

        Storage::disk('private')->assertExists('photo1.jpg');

        $this->assertDatabaseHas('file_manager_acl_rules', ['path' => 'photo1.jpg', 'disk' => 'private','user_id' => $this->user->id]);
    }

    public function test_uploadMethod_doesNotExcept_unSupportedFiles()
    {
        $this->getLoggedInUserForWeb('agent');

        Storage::fake('private');

        $this->uploadFile('video.mp4')->assertStatus(412)
            ->assertJsonFragment(["message" => "Uploaded files must be of type png,gif,jpg,jpeg,zip,rar,doc,docx,xls,xlsx,ppt,pptx,pdf,csv,txt"]);
    }

    public function test_initializeMethod_returnsFileManagerInitialConfiguration_successfully()
    {
        $this->getLoggedInUserForWeb('agent');

        $response = $this->get(route('fm.initialize'));

        $response->assertOk();

        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(['private' => ['driver' => 'local']], $responseArray['config']['disks']);
    }

    public function test_initializeMethod_returnsFileManagerInitialConfiguration_forOnlyPublicDiskWhenPageTypeisKb()
    {
        $this->getLoggedInUserForWeb('agent');

        $response = $this->get(route('fm.initialize', ['page' => 'kb']));

        $response->assertOk();

        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(['public' => ['driver' => 'local']], $responseArray['config']['disks']);
    }

    public function test_initializeMethod_redirectsForNonAgents()
    {
        $this->getLoggedInUserForWeb();

        $this->get(route('fm.initialize', ['page' => 'kb']))->assertStatus(302);
    }

    public function testDeleteMethod_deletesFile_successfully()
    {
        $this->getLoggedInUserForWeb('agent');

        Storage::fake('private');

        $this->uploadFile('test.jpg')->assertOk();

        Storage::disk('private')->assertExists('test.jpg');

        $this->post(route('fm.delete', [
            'disk' => 'private',
            'items' => [['path' => 'test.jpg', 'type' => 'file']]
        ]))->assertOk();

        Storage::disk('private')->assertMissing('test.jpg');
    }
}
