<?php
namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Ticket\Tickets;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Model\helpdesk\Agent_panel\User_org;

class TicketsGraphController extends Controller
{
    /**
     *
     * @param Request $request
     * @return type json
     */
    public function chartData(Request $request)
    {

        
        //taking user-id as an array ,if organization id present we are taking user id based on organization id

        $userId = $request->org_id ?  User_org::where('org_id', $request->org_id[0])->pluck('user_id')->toArray() : $request->user_id;


        $viewAs = $request->view_as;

        
        $start = $request->input('start_date') ? : Carbon::now()->subMonth();
        $end = $request->input('end_date')? : Carbon::now();


        $labels = collect($this->range($start, $end))->keys();

        $open = $this->getValues($start, $end, 'open', '', $userId,$viewAs);


        $closed = $this->getValues($start, $end, 'closed', '', $userId,$viewAs);
        $reopened = $this->getValues($start, $end, 'reopened', '', $userId,$viewAs);

        $openCount = $this->getValues($start, $end, 'open', true, $userId,$viewAs);
        $closedCount = $this->getValues($start, $end, 'closed', true, $userId,$viewAs);
        $reopenedCount = $this->getValues($start, $end, 'reopened', true, $userId,$viewAs);

        $labels = array_map(function ($date) {
            return Carbon::parse($date)->format('j M');
        }, $labels->toArray());

        $outputData = (['labels' => $labels, 'datasets' => [$open, $closed, $reopened], 'count' => ['open' => $openCount, 'closed' => $closedCount, 'reopened' => $reopenedCount]]);

        return successResponse('', $outputData);
    }
    /**
     *
     * @param string $start
     * @param string $end
     * @return int
     */
    public function range($start, $end)
    {
        $end = empty($end) ? Carbon::today() : Carbon::parse($end);
        $start = empty($start) ? (clone $end)->subMonth()->addDay() : Carbon::parse($start);
        $labels = array();
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $labels[$date->format('Y-m-d')] = 0;
        }
        return $labels;
    }
    /**
     *
     * @param string $start
     * @param string $end
     * @param string $type
     * @param type $count
     * @param array $userId
     * @param string $viewAs
     * @return type array
     */
    public function getValues($start, $end, $type = 'open', $count = false, $userId,$viewAs)
    {
        $ticket = $this->tickets($start, $end, $userId,$viewAs);

        
        $tickets = $ticket->select(
            DB::raw('count(open.id) as open'), DB::raw('count(closed.id) as closed'), DB::raw('count(reopened.id) as reopened'), DB::raw("DATE_FORMAT(tickets.updated_at, '%Y-%m-%d') as updated_at")
        )
            ->groupBy('updated_at')
            ->pluck($type, 'updated_at');

        if ($count == true) {
            return $tickets->sum();
        }
        $range = $this->range($start, $end);
        $combine = $tickets->union($range)->toArray();
        ksort($combine);
        $collection = collect($combine);
        $color = $this->color($type);
        return [
            'label' => $color['label'],
            "fill" => false,
            "backgroundColor" => $color['fillColor'],
            "borderColor" => $color['fillColor'],
            "pointBackgroundColor" => "#fff",
            'data' => $collection->values(),
        ];
    }
    /**
     *
     * @param string $type it may be open/closed/reopened
     * @return string
     */
    public function color($type)
    {
        switch ($type) {
            case "open":
                $color['label'] = "Open Tickets";
                $color['fillColor'] = '#6C96DF';
                $color['strokeColor'] = "rgba(255, 99, 132, 0.2)";
                $color['pointColor'] = "rgba(255, 99, 132, 0.2)";
                $color['pointStrokeColor'] = "rgba(255, 99, 132, 0.2)";
                $color['pointHighlightFill'] = "rgba(255, 99, 132, 0.2)";
                $color['pointHighlightStroke'] = "rgba(255, 99, 132, 0.2)";
                break;
            case "closed":
                $color['label'] = "Closed Tickets";
                $color['fillColor'] = '#E3B870';
                $color['strokeColor'] = "rgba(221, 129, 0, 0.94)";
                $color['pointColor'] = "rgba(221, 129, 0, 0.94)";
                $color['pointStrokeColor'] = "rgba(60,141,188,1)";
                $color['pointHighlightFill'] = "#fff";
                $color['pointHighlightStroke'] = "rgba(60,141,188,1)";
                break;
            case "reopened":
                $color['label'] = "Reopened Tickets";
                $color['fillColor'] = "#6DC5B2";
                $color['strokeColor'] = "rgba(0, 149, 115, 0.94)";
                $color['pointColor'] = "rgba(0, 149, 115, 0.94)";
                $color['pointStrokeColor'] = "rgba(60,141,188,1)";
                $color['pointHighlightFill'] = "#fff";
                $color['pointHighlightStroke'] = "rgba(60,141,188,1)";
                break;

        }
        return $color;
    }
    /**
     *
     * @param string $start
     * @param string $end
     * @param array $userId
     * @param string $viewAs
     * @return type
     */
    public function tickets($start, $end, $userId,$viewAs)
    {
        //if multiple user id pass that time considering role as user
        $userRole = (count($userId)==1)? User::where('id', $userId[0])->value('role'):'user';
        //for agent view purpose 
        //agent view we can see as agent or as a requester
        if($viewAs){
            $userRole = 'user';
        }

        $tickets = new Tickets;
        $joined = $tickets->where(function ($query) use ($userId, $userRole) {


                $column = ($userRole != "user") ? 'assigned_to' : 'user_id';
                $query = $query->whereIn($column, $userId);
            
            return $query;
        })
            ->leftJoin('ticket_status', 'tickets.status', '=', 'ticket_status.id')
            ->leftJoin('ticket_status_type', 'ticket_status.purpose_of_status', '=', 'ticket_status_type.id')
            ->leftJoin('ticket_status as reopened', function ($join) {
                return $join->on('tickets.status', '=', 'reopened.id')
                    ->where('tickets.reopened', '>', '0');
            })
            ->leftJoin('ticket_status as closed', function ($join) {
                return $join->on('tickets.status', '=', 'closed.id')
                    ->where('ticket_status_type.name', '=', 'closed');
            })
            ->leftJoin('ticket_status as open', function ($join) {
                return $join->on('tickets.status', '=', 'open.id')
                    ->where('ticket_status_type.name', '=', 'open');
            })

            ->when($start, function ($query) use ($start) {
                return $query->whereDate('tickets.updated_at', ">=", $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->whereDate('tickets.updated_at', "<=", $end);
            });
        return $joined;
    }
}
