<?php

namespace App\TimeTrack\Tests\Agent;

use App\Model\helpdesk\Ticket\Tickets;
use App\TimeTrack\Models\TimeTrack;
use App\User;
use DB;
use Tests\AddOnTestCase;

class TimeTrackTest extends AddOnTestCase
{
    private $ticket_id;

    public function setUp():void
    {
        parent::setUp();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $user_id = factory(User::class)->create(['role' => 'admin'])->id;

        $this->ticket_id = factory(Tickets::class)->create(['user_id' => $user_id, 'status' => 1])->id;

        // Login as admin
        $this->getLoggedInUserForWeb('admin');
    }

    /** @group timetrack */
    public function test_storeTrackTime_storeTimeTrackFailurTest()
    {
        $response = $this->json('POST', route('ticket.timetrack.store', ['ticket' => $this->ticket_id]));

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    /** @group timetrack */
    public function test_storeTrackTime_storeTimeTrackFromPopupFailurTest()
    {
        $response = $this->json('POST', route('ticket.timetrack.store', ['ticket' => $this->ticket_id]), [
            'work_time'  => 147,
            'entrypoint' => 'popup',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    /** @group timetrack */
    public function test_storeTrackTime_storeTimeTrackFromPopupSuccessTest()
    {
        $description = 'This is a test time track';

        $response = $this->json('POST', route('ticket.timetrack.store', ['ticket' => $this->ticket_id]), [
            'description' => $description,
            'work_time'   => 674,
            'entrypoint'  => 'popup',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('time_tracks', ['description' => $description]);
    }

    /** @group timetrack */
    public function test_storeTrackTime_storeTimeTrackFromReplySuccessTest()
    {
        $response = $this->json('POST', route('ticket.timetrack.store', ['ticket' => $this->ticket_id]), [
            'work_time'  => 490,
            'entrypoint' => 'reply',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('time_tracks', ['description' => 'This time is tracked from reply']);
    }

    /** @group timetrack */
    public function test_storeTrackTime_storeTimeTrackFromNoteSuccessTest()
    {
        $response = $this->json('POST', route('ticket.timetrack.store', ['ticket' => $this->ticket_id]), [
            'work_time'  => 120,
            'entrypoint' => 'note',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('time_tracks', ['description' => 'This time is tracked from internal note']);
    }

    /** @group timetrack */
    public function test_getTrackedTimes_getAllTimeTracksForCertainTicket()
    {
        $this->createTimeTracks();

        $response = $this->json('GET', route('ticket.timetrack.getbyticket', ['ticket' => $this->ticket_id]));

        $response->assertStatus(200);

        $response->assertJson(['success' => true]);
    }

    /** @group timetrack */
    public function test_getTrackedTimes_getSpecificTimeTracksForCertainTicket()
    {
        $timeTrack = $this->createTimeTracks();

        $response = $this->json('GET', route('ticket.timetrack.getbyticket', [
            'ticket'    => $this->ticket_id,
            'timeTrack' => $timeTrack->id,
        ]));

        $response->assertStatus(200);

        $response->assertJson(['success' => true]);
    }

    /** @group timetrack */
    public function test_storeTimeTrack_updateSpecificTimeTrackFaliurTest()
    {
        $timeTrack = $this->createTimeTracks();

        $response = $this->json('POST', route('ticket.timetrack.store', [
            'ticket'    => $this->ticket_id,
            'timeTrack' => $timeTrack->id,
        ]));

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    /** @group timetrack */
    public function test_storeTimeTrack_updateSpecificTimeTrackSuccessTest()
    {
        $timeTrack   = $this->createTimeTracks();
        $description = 'This is updated time track';

        $response = $this->json('POST', route('ticket.timetrack.store', [
            'ticket'    => $this->ticket_id,
            'timeTrack' => $timeTrack->id,
        ]), [
            'description' => $description,
            'work_time'   => 224,
            'entrypoint'  => 'popup',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('time_tracks', ['description' => $description]);
    }

    /** @group timetrack */
    public function test_destroyTimeTrack_updateSpecificTimeTrackSuccessTest()
    {
        $timeTrack = $this->createTimeTracks();

        $response = $this->json('DELETE', route('ticket.timetrack.destroy', ['timeTrack' => $timeTrack->id]));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('time_tracks', ['id' => $timeTrack->id]);
    }

    private function createTimeTracks()
    {
        return factory(TimeTrack::class)->create([
            'ticket_id' => $this->ticket_id,
        ]);
    }
}
