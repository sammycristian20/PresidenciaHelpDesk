<?php

namespace App\Http\Controllers\Agent\helpdesk;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Ticket\Tickets;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;

/**
 * DashboardController
 * This controlleris used to fetch dashboard in the agent panel.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class DashboardController extends Controller
{

    /**
     * Create a new controller instance.
     * constructor to check
     * 1. authentication
     * 2. user roles
     * 3. roles must be agent.
     *
     * @return void
     */

    public function __construct()
    {

        // checking for authentication
        $this->middleware('auth');
        // checking if the role is agent
        $this->middleware('role.agent');

    }

    /**
     * Get the dashboard page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        try {
            return view('themes.default1.agent.helpdesk.dashboard.dashboard');
        } catch (Exception $e) {
            return view('themes.default1.agent.helpdesk.dashboard.dashboard');
        }
    }

   /**
     * Fetching dashboard graph data to implement graph.
     *
     * @return type Json
     */
    public function ChartData(Request $request)
    {
        $start          = $request->input('start_date', Carbon::now()->subMonth());
        $end            = $request->input('end_date', Carbon::now());
        $labels         = collect($this->range($start, $end))->keys();
        $open           = $this->getValues($start, $end, 'open');
        $closed         = $this->getValues($start, $end, 'closed');
        $reopned        = $this->getValues($start, $end, 'reopened');
        $dues           = $this->getValues($start, $end, 'dues');
        $open_count     = $this->getValues($start, $end, 'open', true);
        $closed_count   = $this->getValues($start, $end, 'closed', true);
        $reopened_count = $this->getValues($start, $end, 'reopened', true);
        $due_count      = $this->getValues($start, $end, 'dues', true);

        $labels = array_map(function ($date) {
            return Carbon::parse($date)->format('j M');
        }, $labels->toArray());

        return response()->json(['data' => ['labels' => $labels, 'datasets' => [$open, $closed, $reopned, $dues]], 'count' => ['open' => $open_count, 'closed' => $closed_count, 'reopened' => $reopened_count, 'due' => $due_count]]);
    }

    public function range($start, $end)
    {
        $end   = empty($end) ? Carbon::today() : Carbon::parse($end);
        $start = empty($start) ? (clone $end)->subMonth()->addDay() : Carbon::parse($start);

        $lables = array();

        for ($date = $start; $date->lte($end); $date->addDay()) {
            $lables[$date->format('Y-m-d')] = 0;
        }

        return $lables;
    }

    public function getValues($start, $end, $type = 'open', $count = false)
    {
        $ticket  = $this->_tickets($start, $end);
        $tickets = $ticket->select(
            DB::raw('count(open.id) as open'), DB::raw('count(closed.id) as closed'), DB::raw('count(reopned.id) as reopened'), DB::raw('count(due.id) as dues'), DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as created_at")
        )
            ->groupBy('created_at')
            ->pluck($type, 'created_at');

        if ($count == true) {
            return $tickets->sum();
        }
        $range   = $this->range($start, $end);
        $combine = $tickets->union($range)->toArray();
        ksort($combine);
        $collection = collect($combine);
        $color      = $this->color($type);
        return [
            'label'                     => $color['label'],
            "fill"                      => false,
            "lineTension"               => 0.1,
            "backgroundColor"           => $color['fillColor'],
            "borderColor"               => $color['fillColor'],
            "borderCapStyle"            => 'butt',
            "borderDash"                => [],
            "borderDashOffset"          => 0.0,
            "borderJoinStyle"           => 'miter',
            "pointBorderColor"          => $color['fillColor'],
            "pointBackgroundColor"      => "#fff",
            "pointBorderWidth"          => 1,
            "pointHoverRadius"          => 5,
            "pointHoverBackgroundColor" => $color['fillColor'],
            "pointHoverBorderColor"     => $color['fillColor'],
            "pointHoverBorderWidth"     => 2,
            "pointRadius"               => 1,
            "pointHitRadius"            => 10,
            'data'                      => $collection->values(),
            "spanGaps"                  => false,
        ];
    }

    public function color($type)
    {
        switch ($type) {
            case "open":
                $color['label']                = "Open Tickets";
                $color['fillColor']            = '#6C96DF';
                $color['strokeColor']          = "rgba(255, 99, 132, 0.2)";
                $color['pointColor']           = "rgba(255, 99, 132, 0.2)";
                $color['pointStrokeColor']     = "rgba(255, 99, 132, 0.2)";
                $color['pointHighlightFill']   = "rgba(255, 99, 132, 0.2)";
                $color['pointHighlightStroke'] = "rgba(255, 99, 132, 0.2)";
                break;
            case "closed":
                $color['label']                = "Closed Tickets";
                $color['fillColor']            = '#E3B870';
                $color['strokeColor']          = "rgba(221, 129, 0, 0.94)";
                $color['pointColor']           = "rgba(221, 129, 0, 0.94)";
                $color['pointStrokeColor']     = "rgba(60,141,188,1)";
                $color['pointHighlightFill']   = "#fff";
                $color['pointHighlightStroke'] = "rgba(60,141,188,1)";
                break;
            case "reopened":
                $color['label']                = "Reopened Tickets";
                $color['fillColor']            = "#6DC5B2";
                $color['strokeColor']          = "rgba(0, 149, 115, 0.94)";
                $color['pointColor']           = "rgba(0, 149, 115, 0.94)";
                $color['pointStrokeColor']     = "rgba(60,141,188,1)";
                $color['pointHighlightFill']   = "#fff";
                $color['pointHighlightStroke'] = "rgba(60,141,188,1)";
                break;
            case "dues":
                $color['label']                = "Due Tickets";
                $color['fillColor']            = "#A3B952";
                $color['strokeColor']          = "rgba(3, 70, 196, 0.9)";
                $color['pointColor']           = "rgba(3, 70, 196, 0.9)";
                $color['pointStrokeColor']     = "rgba(62,141,188,0.9)";
                $color['pointHighlightFill']   = "#fff";
                $color['pointHighlightStroke'] = "rgba(221,221,221,1)";
                break;
        }
        return $color;
    }

    public function _tickets($start, $end)
    {
        $tickets = new Tickets;
        $joined  = $tickets
            ->leftJoin('ticket_status', 'tickets.status', '=', 'ticket_status.id')
            ->leftJoin('ticket_status_type', 'ticket_status.purpose_of_status', '=', 'ticket_status_type.id')
            ->leftJoin('ticket_status as reopned', function ($join) {
                return $join->on('tickets.status', '=', 'reopned.id')
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
            ->leftJoin('ticket_status as due', function ($join) {
                return $join->on('tickets.status', '=', 'due.id')
                    ->where('tickets.duedate', '<', Carbon::now())
                    ->where('ticket_status_type.name', '=', 'open');
            })
            ->when($start, function ($query) use ($start) {
                return $query->whereDate('tickets.created_at', ">=", $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->whereDate('tickets.created_at', "<=", $end);
            });

        return $joined;
    }

    public function userChartData($id, $team = '', $date111 = '', $date122 = '')
    {
        $date11 = strtotime($date122);
        $date12 = strtotime($date111);
        if ($team != '' && $team != 'team') {
            $date12 = strtotime($team);
            $date11 = strtotime($date111);
        }
        if ($date11 && $date12) {
            $date2 = $date12;
            $date1 = $date11;
        } else {
            // generating current date
            $date2  = strtotime(date('Y-m-d'));
            $date3  = date('Y-m-d');
            $format = 'Y-m-d';
            // generating a date range of 1 month
            $date1 = strtotime(date($format, strtotime('-1 month' . $date3)));
        }
        $return = '';
        $last   = '';
        for ($i = $date1; $i <= $date2; $i = $i + 86400) {
            $thisDate = date('Y-m-d', $i);
            if ($team == 'team') {
                $created  = DB::table('tickets')->select('created_at')->where('team_id', '=', $id)->where('created_at', 'LIKE', '%' . $thisDate . '%')->count();
                $closed   = DB::table('tickets')->select('closed_at')->where('team_id', '=', $id)->where('closed_at', 'LIKE', '%' . $thisDate . '%')->count();
                $reopened = DB::table('tickets')->select('reopened_at')->where('team_id', '=', $id)->where('reopened_at', 'LIKE', '%' . $thisDate . '%')->count();
            } else {
                $userRole = User::whereId($id)->value('role');
                if ($userRole == 'user') {
                    $created  = DB::table('tickets')->select('created_at')->where('user_id', '=', $id)->where('created_at', 'LIKE', '%' . $thisDate . '%')->count();
                    $closed   = DB::table('tickets')->select('closed_at')->where('user_id', '=', $id)->where('closed_at', 'LIKE', '%' . $thisDate . '%')->count();
                    $reopened = DB::table('tickets')->select('reopened_at')->where('user_id', '=', $id)->where('reopened_at', 'LIKE', '%' . $thisDate . '%')->count();
                } else {
                    $created  = DB::table('tickets')->select('created_at')->where('assigned_to', '=', $id)->where('created_at', 'LIKE', '%' . $thisDate . '%')->count();
                    $closed   = DB::table('tickets')->select('closed_at')->where('assigned_to', '=', $id)->where('closed_at', 'LIKE', '%' . $thisDate . '%')->count();
                    $reopened = DB::table('tickets')->select('reopened_at')->where('assigned_to', '=', $id)->where('reopened_at', 'LIKE', '%' . $thisDate . '%')->count();
                }
            }
            $value = ['date' => date('j M', $i), 'open' => $created, 'closed' => $closed, 'reopened' => $reopened];
            $array = array_map('htmlentities', $value);
            $json  = html_entity_decode(json_encode($array));
            $return .= $json . ',';
        }
        $last = rtrim($return, ',');

        return '[' . $last . ']';
    }

    public function departments(Request $request)
    {
        $array      = [];
        $column     = $request->input('column');
        $start_date = Carbon::now()->subMonth();
        $start      = ($request->input('start_date') != '') ? $request->input('start_date') : $start_date;
        $end        = ($request->input('end_date') != '') ? $request->input('end_date') : Carbon::now();
        $tickets    = Tickets::select('id', 'ticket_number');
        if (Auth::user()->role == 'agent') {
            $id      = Auth::user()->id;
            $dept    = DepartmentAssignAgents::where('agent_id', '=', $id)->pluck('department_id')->toArray();
            $tickets = $tickets->whereIn('tickets.dept_id', $dept);
        }
        $tickets = $tickets->rightJoin('ticket_status', 'tickets.status', '=', 'ticket_status.id');
        if ($column == 'departments') {
            $tickets = $tickets
                ->leftJoin('department as dep', 'tickets.dept_id', '=', 'dep.id')
                ->select('dep.name as name', 'ticket_status.name as status', DB::raw('COUNT(ticket_status.name) as count'))
                ->orderBy('name')
                ->groupBy('dep.name', 'ticket_status.name');
        } elseif ($column == 'agents') {
            $tickets = $tickets
                ->join('users as u', function ($q) {
                    return $q->on('tickets.assigned_to', '=', 'u.id')
                        ->where('u.is_delete', '!=', 1)
                        ->where('u.active', '=', 1);
                })
                ->select(
                    DB::raw('
                        if(`u`.`first_name` = "",  CONCAT("<a href=\"user/", u.id, " \"> ",u.user_name, "</a>"), CONCAT("<a href=\"user/", u.id, " \"> ",u.first_name, " ", u.last_name, "</a><div style=\"display:none\">, ",u.user_name, "</div>")) as name'),
                    'ticket_status.name as status',
                    DB::raw('COUNT(ticket_status.name) as count')
                )
                ->where(function ($query) {
                    $query->whereNotNull('tickets.assigned_to')
                        ->orWhere('tickets.assigned_to', '<>', 0);
                })
                ->orderBy('name')
                ->groupBy('ticket_status.name', 'u.email');
        } elseif ($column == 'teams') {
            $tickets = $tickets
                ->RightJoin('teams as t', 'tickets.team_id', '=', 't.id')
                ->select('t.name as name', 'ticket_status.name as status', DB::raw('COUNT(ticket_status.name) as count'))
                ->where(function ($query) {
                    $query->whereNotNull('tickets.team_id')
                        ->orwhere('tickets.team_id', '<>', 0);
                })
                ->orderBy('name')
                ->groupBy('t.name', 'ticket_status.name');
        } elseif ($column == 'helptopics') {
            $tickets = $tickets->leftJoin('help_topic as ht', 'tickets.help_topic_id', '=', 'ht.id')
                ->select('ht.topic as name', 'ticket_status.name as status', DB::raw('COUNT(ticket_status.name) as count'))
                ->orderBy('name')
                ->groupBy('ht.topic', 'ticket_status.name');
        } elseif ($column == 'priority') {
            $tickets = $tickets->leftJoin('ticket_priority as pt', 'tickets.priority_id', '=', 'pt.priority_id')
                ->select('pt.priority as name', 'ticket_status.name as status', DB::raw('COUNT(ticket_status.name) as count'))
                ->orderBy('name')
                ->groupBy('pt.priority', 'ticket_status.name');
        } elseif ($column == 'source') {
            $tickets = $tickets->leftJoin('ticket_source as ts', 'tickets.source', '=', 'ts.id')
                ->select('ts.value as name', 'ticket_status.name as status', DB::raw('COUNT(ticket_status.name) as count'))
                ->orderBy('name')
                ->groupBy('ts.value', 'ticket_status.name');
        } elseif ($column == 'sla-plans') {
            $tickets = $tickets->leftJoin('sla_plan as sp', 'tickets.sla', '=', 'sp.id')
                ->select('sp.name as name', 'ticket_status.name as status', DB::raw('COUNT(ticket_status.name) as count'))
                ->orderBy('name')
                ->groupBy('sp.name', 'ticket_status.name');
        } elseif ($column == 'labels') {
            $tickets = $tickets->rightJoin('filters as label', function ($join) {
                $join->on('tickets.id', '=', 'label.ticket_id')
                    ->where('label.key', '=', 'label');
            })
                ->select('label.value as name', 'ticket_status.name as status', DB::raw('COUNT(ticket_status.name) as count'))
                ->orderBy('name')
                ->groupBy('label.value', 'ticket_status.name');
        } elseif ($column == 'tags') {
            $tickets = $tickets->rightJoin('filters as tag', function ($join) {
                $join->on('tickets.id', '=', 'tag.ticket_id')
                    ->where('tag.key', '=', 'tag');
            })
                ->select('tag.value as name', 'ticket_status.name as status', DB::raw('COUNT(ticket_status.name) as count'))
                ->orderBy('name')
                ->groupBy('tag.value', 'ticket_status.name');
        } elseif ($column == 'type') {
            $tickets = $tickets->rightJoin('ticket_type as t_type', 'tickets.type', '=', 't_type.id')
                ->select('t_type.name as name', 'ticket_status.name as status', DB::raw('COUNT(ticket_status.name) as count'))
                ->orderBy('name')
                ->groupBy('t_type.name', 'ticket_status.name');
        } else {
            $tickets = $tickets->rightJoin('ticket_form_data as tfd', function ($join) use ($column) {
                $join->on('tickets.id', '=', 'tfd.ticket_id')
                    ->where('tfd.key', '=', $column)
                    ->where('tfd.content', '!=', '');
            })
                ->select('tfd.content as name', 'ticket_status.name as status', DB::raw('COUNT(ticket_status.name) as count'))
                ->orderBy('name')
                ->groupBy('tfd.content', 'ticket_status.name');
        }
        $tickets = $tickets->where('tickets.created_at', ">=", date('Y-m-d 00:00:00 ', strtotime($start)))
            ->where('tickets.created_at', "<=", date('Y-m-d 23:59:59', strtotime($end)))
            ->get();
        return $tickets->toJson();
    }
}
