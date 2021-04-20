<?php

namespace App\TimeTrack\Controllers;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Ticket\Tickets;
use App\TimeTrack\Models\TimeTrack;
use App\TimeTrack\Requests\TimeTrackRequest;
use Exception;
use Lang;

/**
 * Time track controller
 *
 * @abstract Controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name TimeTrackController
 *
 */
class TimeTrackController extends Controller
{
    /**
     * Get tracked times for a specific ticket
     * @param $ticket Tickets instance
     * @param $timeTrack TimeTrack instance default null
     * @return Json Http response
     *
     * Removing route model binging for timetrach as it creates issue in encoded version
     * @todo find the cause of issue in encoding files regarding Route model binding
     */
    public function getTrackedTimes(Tickets $ticket, int $timeTrack = null)
    {
        try {
            if (is_null($timeTrack)) {
                $timeTrackDetails = $ticket->timeTracks()->latest()->paginate(10);
            } else {
                $timeTrack = TimeTrack::where('id', $timeTrack)->first();
                $timeTrackDetails = $timeTrack;
            }

            return successResponse('', $timeTrackDetails);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }

    }

    /**
     * Store time track for a ticket
     * @param $request TimeTrackRequest instance
     * @param $ticket Tickets instance
     * @param $timeTrack TimeTrack instance default null
     * @return Json Http Error or success message
     *
     * Removing route model binging for timetrach as it creates issue in encoded version
     * @todo find the cause of issue in encoding files regarding Route model binding
     */
    public function storeTimeTrack(TimeTrackRequest $request, Tickets $ticket, int $timeTrack = null)
    {
        // Set user description
        $description = $request->description;

        // Handle description field if entry point is other than popup
        if ($request->entrypoint == 'reply') {
            $description = 'This time is tracked from reply';
        }

        if ($request->entrypoint == 'note') {
            $description = 'This time is tracked from internal note';
        }

        // Store time track data in db
        try {
            // Check create or update action
            if (is_null($timeTrack)) {
                $ticket->timeTracks()->create([
                    'description' => $description,
                    'work_time'   => $request->work_time,
                ]);
            } else {
                $timeTrack = TimeTrack::where('id', $timeTrack)->first();
                $timeTrack->description = $description;
                $timeTrack->work_time   = $request->work_time;

                $timeTrack->save();
            }

            return successResponse(Lang::get('timetrack::lang.saved-successfully'));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Remove a time track record
     *
     * @param $timeTrack TimeTrack instance
     * @return Json Http response
     */
    public function destroyTimeTrack(TimeTrack $timeTrack)
    {
        try {
            $timeTrack->delete();

            return successResponse(Lang::get('timetrack::lang.removed-successfully'));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
}
