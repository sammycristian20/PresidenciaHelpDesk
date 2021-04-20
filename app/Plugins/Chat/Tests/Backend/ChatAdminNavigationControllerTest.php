<?php
namespace App\Plugins\Chat\Tests\Backend;

use App\Plugins\Chat\Controllers\ChatAdminNavigationController;

use Tests\AddOnTestCase;

class ChatAdminNavigationControllerTest extends AddOnTestCase
{

    private $adminNavigation;

    public function setUp():void
    {
        parent::setUp();

        $this->adminNavigation = new ChatAdminNavigationController;
    }

    /** @group injectChatAdminNavigation */
    public function test_injectChatAdminNavigation_forSuccess()
    {
    $navigationContainer = collect();

    $this->adminNavigation->injectChatAdminNavigation($navigationContainer);

    $this->assertEquals('Chat', $navigationContainer[0]->name);
    $navigations = $navigationContainer[0]->navigations;
    $this->assertCount(1, $navigations);
    $this->assertEquals('Chat Settings', $navigations[0]->name);
    }
}
