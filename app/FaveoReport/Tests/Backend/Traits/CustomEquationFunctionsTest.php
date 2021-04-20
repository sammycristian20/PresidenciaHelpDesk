<?php

namespace App\FaveoReport\Tests\Backend\Traits;

use Tests\DBTestCase;
use App\FaveoReport\Traits\CustomEquationFunctions;
use App\Model\Common\TicketActivityLog;
use App\Model\helpdesk\Ticket\Tickets;

class CustomEquationFunctionsTest extends DBTestCase
{
  use CustomEquationFunctions;

  public function test_lastStatusChange_whenAnInvalidStatusIsPassed_shouldReturnNUll()
  {
    $row = (object)[];
    $methodResponse = $this->lastStatusChange('invalid_status', $row);
    $this->assertNull($methodResponse);
  }

  public function test_lastStatusChange_whenAnValidStatusIsPassedButNoRecordsOfThatIsFoundInActivityLog_shouldReturnNUll()
  {
    $ticket = factory(Tickets::class)->create();
    $methodResponse = $this->lastStatusChange('Open', $ticket);

    $this->assertNull($methodResponse);
  }

  public function test_lastStatusChange_whenAnValidStatusIsPassedAndRecordsOfThatIsFoundInActivityLog_shouldReturnTimestampOfTheRecord()
  {
    $ticket = factory(Tickets::class)->create();

    $activityTime = TicketActivityLog::create(['field'=>'status_id', 'value'=>1, 'ticket_id'=>$ticket->id]);

    $methodResponse = $this->lastStatusChange('Open', $ticket);

    $this->assertEquals($activityTime->created_at->timestamp,$methodResponse);
  }
}
