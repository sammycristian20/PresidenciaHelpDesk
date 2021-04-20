<?php

namespace App\FaveoLog\Tests\Backend;

use Tests\DBTestCase;
use App\FaveoLog\Model\MailLog;
use App\Model\helpdesk\Ticket\EmailThread;
use App\FaveoLog\Model\LogCategory;

class MailLogModelTest extends DBTestCase
{

    /** @group getMailLogStatusAttribute */
    public function test_getStatusAttribute_whenStatusIsNonEmpty()
    {
        $mailLogId = MailLog::create(['status' => 'NON_EMPTY'])->id;
        $this->assertEquals(MailLog::find($mailLogId)->status, 'NON_EMPTY');
    }

    /** @group getMailLogStatusAttribute */
    public function test_getStatusAttribute_whenStatusIsEmptyButCategoryIsNotMailfetch()
    {
        $categoryId = LogCategory::updateOrCreate(['name' => 'test_category'])->id;
        $mailLogId = MailLog::create(['log_category_id' => $categoryId])->id;
        $this->assertEquals(MailLog::find($mailLogId)->status, '');
    }

    /** @group getMailLogStatusAttribute */
    public function test_getStatusAttribute_whenStatusIsEmptyAndCategoryMailfetchButTicketIsNotCreated()
    {
        $categoryId = LogCategory::updateOrCreate(['name' => 'mail-fetch'])->id;
        EmailThread::create(['message_id' => 'test_message_id']);
        $mailLogId = MailLog::create(['log_category_id' => $categoryId, 'referee_id' => 'wrong_message_id'])->id;
        $this->assertEquals(MailLog::find($mailLogId)->status, 'rejected');

        // it should save in the database too
        $this->assertEquals(MailLog::find($mailLogId)->getOriginal()['status'], 'rejected');
    }

    /** @group getMailLogStatusAttribute */
    public function test_getStatusAttribute_whenStatusIsEmptyAndCategoryMailfetchAndTicketIsCreated()
    {
        $categoryId = LogCategory::updateOrCreate(['name' => 'mail-fetch'])->id;
        EmailThread::create(['message_id' => 'test_message_id']);
        $mailLogId = MailLog::create(['log_category_id' => $categoryId, 'referee_id' => 'test_message_id'])->id;
        $this->assertEquals(MailLog::find($mailLogId)->status, 'accepted');

        // it should save in the database too
        $this->assertEquals(MailLog::find($mailLogId)->getOriginal()['status'], 'accepted');
    }

    public function test_setCollaboratorsAttribute_whenValuePassedIsNonArray_shouldSaveJsonEncodeOfAnEmptyArray()
    {
        $categoryId = LogCategory::updateOrCreate(['name' => 'mail-fetch'])->id;
        $mailLogId = MailLog::create(['log_category_id' => $categoryId, 'collaborators' => 'string'])->id;
        $this->assertEquals(json_encode([]), MailLog::find($mailLogId)->getOriginal()['collaborators']);
    }

    public function test_setCollaboratorsAttribute_whenValuePassedIsArray_shouldSaveJsonEncodeOfThatArray()
    {
        $categoryId = LogCategory::updateOrCreate(['name' => 'mail-fetch'])->id;
        $mailLogId = MailLog::create(['log_category_id' => $categoryId, 'collaborators' => ['email@email.com']])->id;
        $this->assertEquals(json_encode(['email@email.com']), MailLog::find($mailLogId)->getOriginal()['collaborators']);
    }

    public function test_getCollaboratorsAttribute_whenValueNull_shouldReturnAnEmptyArray()
    {
        $categoryId = LogCategory::updateOrCreate(['name' => 'mail-fetch'])->id;
        // using DB facade to avoid model attribute trigger
        \DB::table("mail_logs")->insert(['log_category_id' => $categoryId, 'collaborators' => null]);
        $collaborators = MailLog::orderBy("id", "desc")->value("collaborators");
        $this->assertEquals([], $collaborators);
    }
}
