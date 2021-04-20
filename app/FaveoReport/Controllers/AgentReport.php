<?php

namespace App\FaveoReport\Controllers;

use App\FaveoReport\Controllers\ReportIndepth;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\User;
use Carbon\Carbon;
use DB;
use Exception;

/**
 * Agent report
 *
 * @abstract ReportController
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name AgentReport
 *
 */
class AgentReport extends ReportIndepth
{
    /**
     *
     * get the tickets of an agent
     *
     * @return json
     */
    public function agent()
    {
        $chart    = $this->request->input('chart', 'line');
        $category = $this->request->input('category', 'week');
        $join     = $this->tickets();
        $tickets  = $join
            ->when($category, function ($query) use ($category) {
                $ranges = $this->dateRange($category);
                return $query->whereBetween('tickets.created_at', $ranges)
                    ->groupBy('date');
            })
            ->select(DB::raw('COUNT(tickets.id) as tickets'), 'agent.user_name', 'agent.first_name', 'agent.last_name', 'agent.id', DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date"))
            ->groupBy('agent.id')
            ->get();

        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     *
     * Get the agent's ticket according to status
     *
     * @return type
     */
    public function agentStatus()
    {
        $chart    = $this->request->input('chart');
        $category = $this->request->input('category');
        $agentid  = $this->request->input('agent', 'vijay');
        $join     = $this->tickets();
        $tickets  = $join
            ->when($agentid, function ($query) use ($agentid) {
                return $query->where('agent.user_name', $agentid);
            });
        return $tickets;
    }

    /**
     *
     * Get the chart of agent
     *
     * @return json
     */
    public function agentChart()
    {
        $chart    = $this->request->input('chart', 'line');
        $category = $this->request->input('category', 'week');
        $tickets  = $this->agentStatus()
            ->select(DB::raw('COUNT(tickets.id) as tickets'), 'agent.user_name', 'agent.first_name', 'agent.last_name', 'agent.id', 'ticket_status.name', DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date"))
            ->when($category, function ($query) use ($category) {
                $ranges = $this->dateRange($category);
                return $query->whereBetween('tickets.created_at', $ranges)
                    ->groupBy('date');
            })
            ->groupBy('ticket_status.name')
            ->get();

        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     *
     * get agent report view
     *
     * @return view
     */
    public function getAgent()
    {
        try {
            if (!$this->policy->report()) {
                return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
            }
            $agents = User::where('role', '!=', 'user')->pluck('user_name', 'user_name')->toArray();
            $table  = Ticket_Status::pluck('name')->toArray();
            arsort($table);
            return view('report::agents.details', compact('agents', 'table'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     *
     * get the Agent's ticket according to ticket status
     *
     * @param boolean $table
     * @return collection|json
     */
    public function getAgentStatusTable($table = true)
    {
        $start   = $this->request->input("agent_export_start_date");
        $end     = $this->request->input("agent_export_end_date");
        $tickets = $this->agentStatus()
            ->where('agent.role', '!=', 'user')
            ->whereNotNull('agent.id')
            ->when($start, function ($query) use ($start) {
                return $query->whereDate('tickets.created_at', '>', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->whereDate('tickets.created_at', '>', $end);
            })
            ->select(DB::raw('COUNT(tickets.id) as tickets'), 'agent.user_name', 'agent.first_name', 'agent.last_name', 'agent.id', 'ticket_status.name as status')
            ->groupBy('agent.id', 'ticket_status.name');
        $collection = $tickets->get()->groupBy('id');
        $agents     = collect([]);
        if ($collection->count() > 0) {
            $agents = $this->refin($collection);
        }
        if ($table == true) {
            return \Datatables::of($agents)
                ->make();
        } else {
            return $agents;
        }
    }

    /**
     *
     * refine the collection data
     *
     * @param collection $collections
     * @return collection
     */
    public function refin($collections)
    {
        $collections->transform(function ($items, $key) {
            foreach ($items as $item) {
                $agent['zz_user_name']  = $item['user_name'];
                $agent['zz_first_name'] = $item['first_name'];
                $agent['zz_last_name']  = $item['last_name'];
                if ($item['status'] != null) {
                    $agent[$item['status']] = $item['tickets'];
                }
            }
            return collect($agent);
        });
        $statuses = Ticket_Status::select('name')->get();
        foreach ($statuses as $status) {
            foreach ($collections as $agent_tickets) {
                if (!$agent_tickets->has($status->name)) {
                    $agent_tickets->put($status->name, 0);
                }
            }
        }
        foreach ($collections as $key => $sort) {
            $arrays[$key] = $sort->toArray();
            krsort($arrays[$key]);
        }
        return collect($arrays);
    }

    /**
     *
     * Export the agent report
     *
     * @param boolean $storage
     * @return excel sheet
     */
    public function AgentExport($storage = true, $mail = false)
    {
        $storage_path = storage_path('exports');

        if (is_dir($storage_path)) {
            delTree($storage_path);
        }

        $tickets   = $this->agentPerformance();
        $agentData = array();

        foreach ($tickets as $ticket) {
            $responseSla       = '0';
            $resolutionSla     = '0';
            $avgResponseTime   = '--';
            $avgResolutionTime = '--';

            if ($ticket->success_response_sla !== '0') {
                $per         = round(($ticket->success_response_sla / $ticket->assigned_tickets) * 100, 2);
                $responseSla = $ticket->success_response_sla . " ($per%)";
            }

            if ($ticket->success_resolution_sla !== '0') {
                $per           = round(($ticket->success_resolution_sla / $ticket->assigned_tickets) * 100, 2);
                $resolutionSla = $ticket->success_resolution_sla . " ($per%)";
            }

            if ($ticket->assigned->avgResponseTime()) {
                $avgResponseTime = convertToHours(abs($ticket->assigned->avgResponseTime()), '%02dh %02dm');
            }

            if ($ticket->avg_resolution_time) {
                $avgResolutionTime = convertToHours(abs($ticket->avg_resolution_time), '%02dh %02dm');
            }

            $agentData[] = [
                'Agent'                   => $ticket->assigned->full_name,
                'Tickets Assigned'        => $ticket->assigned_tickets,
                'Tickets Resolved'        => $ticket->closed_tickets,
                'Tickets Reopened'        => $ticket->reopened,
                'First Response SLA'      => $responseSla,
                'Resolution SLA'          => $resolutionSla,
                'Responses'               => $ticket->assigned->responses() ? $ticket->assigned->responses() : 0,
                'Average Response Time'   => $avgResponseTime,
                'Average Resolution Time' => $avgResolutionTime,
            ];
        }

        if (empty($agentData)) {
            return 'No data to export';
        }

        $excel = \Excel::create('agent_performance_report-' . faveoDate(null, 'dmYhmi'), function ($excel) use ($agentData) {
            $excel->sheet('sheet', function ($sheet) use ($agentData) {
                $sheet->fromArray($agentData);

                $sheet->row(1, function ($row) {
                    $row->setFontSize(12);
                    $row->setFontWeight('bold');
                    $row->setAlignment('center');
                });
            });
        });

        if ($storage === false) {
            $excel->download('xls');
        } else {
            $path = $excel->store('xls', false, true);
        }

        if ($mail == true) {
            return $path;
        }

        return 'success';
    }

    /**
     *
     * Send mail of agent report
     *
     * @return mail
     */
    public function mail()
    {
        $this->validate($this->request, [
            'to'      => 'required',
            'subject' => 'max|20',
        ]);

        try {
            $path = $this->AgentExport(true);
            $this->mailing($path, 'agents');
            $message     = "Mail has sent";
            $status_code = 200;
        } catch (Exception $ex) {
            $message     = [$ex->getMessage()];
            $status_code = 500;
        }
        return $this->mailResponse($message, $status_code);
    }

    /**
     * agent permormance view
     * @return view
     */
    public function getView()
    {
        if (!$this->policy->report()) {
            return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
        }

        $maxDateRange = (new CommonSettings)->getOptionValue('reports_max_date_range')->first()->option_value;

        return view("report::agents.performance", compact('maxDateRange'));
    }

    /**
     * get agent's performance details in json
     * @return json
     */
    public function agentPerformance()
    {
        try {
            $agents      = $this->request->input('agents');
            $departmets  = $this->request->input('departments');
            $client      = $this->request->input('clients');
            $source      = $this->request->input('sources');
            $priority    = $this->request->input('priorities');
            $type        = $this->request->input('types');
            $status      = $this->request->input('status');
            $helptopic   = $this->request->input('helptopic');
            $team        = $this->request->input('team');
            $createStart = $this->request->input('start_date');
            $createEnd   = $this->request->input('end_date');
            $updateStart = $this->request->input('update_start');
            $updateEnd   = $this->request->input('update_end');

            if (empty($createStart) && empty($updateStart)) {
                $createStart = Carbon::now()->subMonth()->addDay()->format('Y-m-d');
                $createEnd   = Carbon::now()->format('Y-m-d');
            }

            $ticket = Tickets::
                // Filtering by created at
                when($createStart, function ($q) use ($createStart, $createEnd) {
                $q->whereBetween('tickets.created_at', [
                    Carbon::parse($createStart),
                    Carbon::parse($createEnd)->endOfDay(),
                ]);
            })

            // Filtering by updated at
                ->when($updateStart, function ($q) use ($updateStart, $updateEnd) {
                    $q->whereBetween('tickets.updated_at', [
                        Carbon::parse($updateStart),
                        Carbon::parse($updateEnd)->endOfDay(),
                    ]);
                })
                ->whereNotNull('assigned_to')
                ->when($agents, function ($q) use ($agents) {
                    $q->whereIn('assigned_to', $agents);
                })
                ->when($departmets, function ($q) use ($departmets) {
                    $q->whereIn('dept_id', $departmets);
                })
                ->when($client, function ($q) use ($client) {
                    $q->whereIn('user_id', $client);
                })
                ->when($source, function ($q) use ($source) {
                    $q->whereIn('source', $source);
                })
                ->when($priority, function ($q) use ($priority) {
                    $q->whereIn('priority_id', $priority);
                })
                ->when($type, function ($q) use ($type) {
                    $q->whereIn('type', $type);
                })

            // Filtering by status
                ->when($status, function ($query) use ($status) {
                    return $query->whereIn('tickets.status', $status);
                })

            // Filtering by helptopic
                ->when($helptopic, function ($query) use ($helptopic) {
                    return $query->whereIn('tickets.help_topic_id', $helptopic);
                })

            // Filtering by team
                ->when($team, function ($query) use ($team) {
                    return $query->whereIn('tickets.team_id', $team);
                })

                ->select(
                    \DB::raw('count(distinct tickets.id) as assigned_tickets'), \DB::raw('SUM(CASE WHEN tickets.reopened > 0 THEN 1 ELSE 0 END) AS reopened'), \DB::raw('SUM(CASE WHEN tickets.is_response_sla = 1 THEN 1 ELSE 0 END) AS success_response_sla'), \DB::raw('SUM(CASE WHEN tickets.is_resolution_sla = 1 THEN 1 ELSE 0 END) AS success_resolution_sla'), \DB::raw('AVG(tickets.resolution_time) as avg_resolution_time'), \DB::raw('count(open_status.id) as open_tickets'), \DB::raw('count(close_status.id) as closed_tickets'), 'assigned_to', 'tickets.id', 'tickets.status'
                )
                ->with(['assigned' => function ($q) {
                    $q->select('id', 'first_name', 'last_name', 'user_name', 'email');
                }])
                ->leftJoin('ticket_status', 'tickets.status', '=', 'ticket_status.id')
                ->leftJoin('ticket_status_type as open_status', function ($join) {
                    $join->on('open_status.id', '=', 'ticket_status.purpose_of_status')
                        ->where('open_status.name', '=', 'open');
                })
                ->leftJoin('ticket_status_type as close_status', function ($join) {
                    $join->on('close_status.id', '=', 'ticket_status.purpose_of_status')
                        ->whereIn('close_status.name', ['closed', 'deleted']);
                })
                ->groupBy('assigned_to')
                ->get();
        } catch (\Exception $ex) {
            throw new Exception("Failed to generate agent performance. Exception: " . $ex->getMessage());
        }

        return $ticket;
    }

    /**
     * tickets query builder for agent performance
     * @return querybuilder
     */
    public function thisTickets()
    {
        return Tickets::
            join('users as agent', 'tickets.assigned_to', '=', 'agent.id')
            ->leftJoin('ticket_status', 'tickets.status', '=', 'ticket_status.id')
            ->leftJoin('ticket_status_type as open_status', function ($join) {
                $join->on('open_status.id', '=', 'ticket_status.purpose_of_status')
                    ->where('open_status.name', '=', 'open');
            })
            ->leftJoin('ticket_status_type as close_status', function ($join) {
                $join->on('close_status.id', '=', 'ticket_status.purpose_of_status')
                    ->where('close_status.name', '=', 'closed');
            })
            ->leftJoin('ticket_thread', function ($join) {
                $join->on('tickets.id', '=', 'ticket_thread.ticket_id')
                    ->where('ticket_thread.poster', '=', 'support')
                    ->where('ticket_thread.is_internal', '=', '0');
            })
            ->leftJoin('ticket_priority', 'tickets.priority_id', '=', 'ticket_priority.priority_id')
        ;
    }

    /**
     *
     * @param object $schema
     * @param boolean $force
     * @return mixed
     */
    public function addConditionToSchema($schema, $force = false)
    {
        $agents     = $this->request->input('agents');
        $departmets = $this->request->input('departments');
        $client     = $this->request->input('clients');
        $source     = $this->request->input('sources');
        $priority   = $this->request->input('priorities');
        $type       = $this->request->input('types');
        $end_date   = Carbon::now();
        $start_date = Carbon::now()->subMonth()->format('Y-m-d');
        $start      = $this->request->input('start_date');
        $end        = $this->request->input('end_date');
        if (!$start) {
            $start = $start_date;
        }
        if (!$end) {
            $end = $end_date->format('Y-m-d');
        }
        if ($force != true) {
            $schema = $schema
                ->when($agents, function ($query) use ($agents) {
                    return $query
                    //->join('users as agent', 'tickets.assigned_to', '=', 'agent.id')
                    ->whereIn('agent.id', $agents);
                })
                ->when($departmets, function ($query) use ($departmets) {
                    return $query
                        ->join('department', 'tickets.dept_id', '=', 'department.id')
                        ->whereIn('department.id', $departmets);
                })
                ->when($client, function ($query) use ($client) {
                    return $query
                        ->join('users as client', 'tickets.user_id', '=', 'client.id')
                        ->whereIn('client.id', $client);
                })
                ->when($source, function ($query) use ($source) {
                    return $query->whereIn('ticket_source.id', $source);
                })
                ->when($priority, function ($query) use ($priority) {
                    return $query->whereIn('ticket_priority.priority_id', $priority);
                })
                ->when($type, function ($query) use ($type) {
                    return $query->whereIn('type.id', $type);
                })
                ->whereDate('tickets.created_at', ">=", $start)
                ->whereDate('tickets.created_at', "<=", $end)
            ;
        }

        return $schema;
    }

    /**
     * get the deatils in json form datatable
     * @return json
     */
    public function agentDatatable()
    {
        $collection = $this->agentPerformance();

        return \DataTables::of($collection)
            ->editColumn('agent', function ($ticket) {
                return $ticket->assigned->full_name;
            })
            ->addColumn('avg_response_time', function ($ticket) {
                $hours = "--";
                if ($ticket->assigned->avgResponseTime()) {
                    $hours = convertToHours(abs($ticket->assigned->avgResponseTime()), '%02dh %02dm');
                }
                return $hours;
            })
            ->addColumn('avg_resolution_time', function ($ticket) {
                $hours = "--";
                if ($ticket->avg_resolution_time) {
                    $hours = convertToHours(abs($ticket->avg_resolution_time), '%02dh %02dm');
                }
                return $hours;
            })
            ->addColumn('success_response_sla', function ($ticket) {
                $hours = 0;
                if ($ticket->success_response_sla !== '0') {
                    $per   = round(($ticket->success_response_sla / $ticket->assigned_tickets) * 100, 2);
                    $hours = $ticket->success_response_sla . " ($per%)";
                }
                return $hours;
            })
            ->addColumn('success_resolution_sla', function ($ticket) {
                $hours = 0;
                if ($ticket->success_resolution_sla !== '0') {
                    $per   = round(($ticket->success_resolution_sla / $ticket->assigned_tickets) * 100, 2);
                    $hours = $ticket->success_resolution_sla . " ($per%)";
                }
                return $hours;
            })
            ->addColumn('number_of_response', function ($ticket) {
                $responses = 0;
                if ($ticket->assigned->responses()) {
                    $responses = $ticket->assigned->responses();
                }
                return $responses;
            })
            ->make(true);
    }
}
