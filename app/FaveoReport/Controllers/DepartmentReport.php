<?php

namespace App\FaveoReport\Controllers;

use App\FaveoReport\Controllers\ReportController;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Status;
use Carbon\Carbon;
use DB;
use Exception;

/**
 * Department report
 *
 * @abstract ReportController
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name DepartmentReport
 *
 */
class DepartmentReport extends ReportController
{
    /**
     *
     * get all ticket according department
     *
     * @return json
     */
    public function departmentAll()
    {
        $chart    = $this->request->input('chart');
        $category = $this->request->input('category');
        $tickets  = $this->departmentStatus()
            ->select(DB::raw('COUNT(tickets.id) as tickets'), 'department.name')
            ->groupBy('department.name')
            ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     *
     * get ticket according department as well as priority
     *
     * @return json
     */
    public function departmentPriority()
    {
        $chart    = $this->request->input('chart');
        $category = $this->request->input('category');
        $tickets  = $this->departmentStatus()
            ->select(DB::raw('COUNT(tickets.id) as tickets'), 'department.name', 'ticket_priority.priority')
            ->groupBy('department.name', 'ticket_priority.priority')
            ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     *
     * get ticket according department as well as Sla
     *
     * @return json
     */
    public function departmentSla()
    {
        $chart    = $this->request->input('chart');
        $category = $this->request->input('category');
        $tickets  = $this->departmentStatus()
            ->select(DB::raw('COUNT(tickets.id) as tickets'), 'department.name', 'sla_plan.name')
            ->groupBy('department.name', 'sla_plan.name')
            ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     *
     * get ticket according department as well as Helptopic
     *
     * @return json
     */
    public function departmentHelptopic()
    {
        $chart    = $this->request->input('chart');
        $category = $this->request->input('category');
        $tickets  = $this->departmentStatus()
            ->select(DB::raw('COUNT(tickets.id) as tickets'), 'department.name', 'help_topic.topic')
            ->groupBy('department.name', 'help_topic.topic')
            ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     *
     * get ticket according department as well as status
     *
     * @return json
     */
    public function departmentStatus()
    {
        $chart      = $this->request->input('chart');
        $category   = $this->request->input('category');
        $department = $this->request->input('department');
        $join       = $this->tickets();
        $tickets    = $join
            ->when($department, function ($query) use ($department) {
                return $query->where('department.name', '=', $department);
            });

        return $tickets;
    }

    /**
     *
     * get ticket according department chart
     *
     * @return json
     */
    public function departmentChart()
    {
        $chart    = $this->request->input('chart');
        $category = $this->request->input('category');
        $tickets  = $this->departmentStatus()
            ->when($category, function ($query) use ($category) {
                $ranges = $this->dateRange($category);
                return $query->whereBetween('tickets.created_at', $ranges)->groupBy('date');
            })
            ->select(DB::raw('COUNT(tickets.id) as tickets'), 'department.name', 'ticket_status.name', DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date"))
            ->groupBy('department.name', 'ticket_status.name')
            ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     *
     * get ticket according department as well as stattus type
     *
     * @return json
     */
    public function departmentStatusType()
    {
        $chart    = $this->request->input('chart');
        $category = $this->request->input('category');
        $tickets  = $this->departmentStatus()
            ->select(DB::raw('COUNT(tickets.id) as tickets'), 'department.name', 'ticket_status_type.name')
            ->groupBy('department.name', 'ticket_status_type.name')
            ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     *
     * get ticket according department as well as source
     *
     * @return json
     */
    public function departmentSource()
    {
        $chart    = $this->request->input('chart');
        $category = $this->request->input('category');
        $tickets  = $this->departmentStatus()
            ->select(DB::raw('COUNT(tickets.id) as tickets'), 'department.name', 'ticket_source.name')
            ->groupBy('department.name', 'ticket_source.name')
            ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     *
     * get the report department report page (view)
     *
     * @return view
     */
    public function department()
    {
        try {
            if (!$this->policy->report()) {
                return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
            }
            $table = Ticket_Status::pluck('name')->toArray();
            arsort($table);
            $departments = Department::pluck('name', 'name')->toArray();
            return view('report::department.department', compact('departments', 'table'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     *
     * get table of department ticket
     *
     * @param boolean $table
     * @return collection|json
     */
    public function datatable($table = true)
    {
        $starting = $this->request->input("start");
        $ending   = $this->request->input("end");
        $join     = $this->tickets();
        $tickets  = $join
            ->when($starting, function ($query) use ($starting, $ending) {
                $start = $this->getCarbon($starting, "-", "Y-m-d");
                $end   = $this->getCarbon($ending, "-", "Y-m-d", false);
                return $query->whereBetween('tickets.created_at', [$start, $end]);
            })
            ->whereNotNull('teams.name')
            ->select(
                DB::raw('COUNT(tickets.id) as tickets'), 'department.name as department', 'ticket_status.name as status'
            )
            ->groupBy('department.name', 'ticket_status.name')
            ->get();
        $collection = collect([]);
        if (count($tickets) > 0) {
            $collection = $this->refine($tickets);
        }
        if ($table == true) {
            return \Datatables::of($collection)
                ->make();
        } else {
            return $collection;
        }
    }

    /**
     *
     * reine the department ticket collection
     *
     * @param object $tickets
     * @return collection
     */
    public function refine($tickets)
    {
        $collection = $tickets->groupBy('department')->transform(function ($items, $key) {

            foreach ($items as $item) {
                $team['zzzdepartment'] = $item->department;
                $team[$item->status]   = $item->tickets;
            }
            return collect($team);
        });

        $statuses = Ticket_Status::select('name')->get();
        $teams    = $collection->transform(function ($item, $key) use ($statuses) {

            foreach ($statuses as $status) {
                if (!$item->has($status->name)) {
                    $item->put($status->name, 0);
                }
            }
            return $item;
        });

        $teams_obj = Department::select('name')->get();
        foreach ($teams_obj as $k => $one) {
            if (!$teams->has($one->name)) {
                foreach ($teams->first() as $key => $it) {
                    if ($key == "zzzdepartment") {
                        $new[$key] = $one->name;
                    } else {
                        $new[$key] = 0;
                    }
                }
                $teams->push(collect($new));
            }
        }
        return $this->sort($teams);
    }

    /**
     *
     * sorting the array
     *
     * @param object $collection
     * @return collection
     */
    public function sort($collection)
    {
        $array = $collection->toArray();
        foreach ($array as $item) {
            krsort($item);
            $new[] = $item;
        }
        return collect($new);
    }

    /**
     *
     * export the ticket details of department
     *
     * @param boolean $storage
     * @return excel page
     */
    public function departmentExport($storage = true, $mail = false)
    {
        $storage_path = storage_path('exports');

        if (is_dir($storage_path)) {
            delTree($storage_path);
        }

        $tickets  = $this->departmentPerformance();
        $deptData = array();

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

            if ($ticket->departments->avgResponseTime()) {
                $avgResponseTime = convertToHours(abs($ticket->departments->avgResponseTime()), '%02dh %02dm');
            }

            if ($ticket->avg_resolution_time) {
                $avgResolutionTime = convertToHours(abs($ticket->avg_resolution_time), '%02dh %02dm');
            }

            $deptData[] = [
                'Department'              => $ticket->departments->name,
                'Tickets Assigned'        => $ticket->assigned_tickets,
                'Tickets Resolved'        => $ticket->closed_tickets,
                'Tickets Reopened'        => $ticket->reopened,
                'First Response SLA'      => $responseSla,
                'Resolution SLA'          => $resolutionSla,
                'Responses'               => $ticket->departments->responses() ? $ticket->departments->responses() : 0,
                'Average Response Time'   => $avgResponseTime,
                'Average Resolution Time' => $avgResolutionTime,
            ];
        }

        if (empty($deptData)) {
            return 'No data to export';
        }

        $excel = \Excel::create('department_performance_report-' . faveoDate(null, 'dmYhmi'), function ($excel) use ($deptData) {
            $excel->sheet('sheet', function ($sheet) use ($deptData) {
                $sheet->fromArray($deptData);

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
     * mailing the department ticket
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
            $path = $this->departmentExport(true);
            $this->mailing($path, 'departments');
            $message     = "Mail has sent";
            $status_code = 200;
        } catch (Exception $ex) {
            $message     = [$ex->getMessage()];
            $status_code = 500;
        }
        return $this->mailResponse($message, $status_code);
    }

    /**
     * get ticket query builder for department performance
     * @return object
     */
    public function thisTickets()
    {
        return Tickets::
            join('department', 'tickets.dept_id', '=', 'department.id')
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
     * get view of performance
     * @return view
     */
    public function getView()
    {
        if (!$this->policy->report()) {
            return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
        }

        $maxDateRange = (new CommonSettings)->getOptionValue('reports_max_date_range')->first()->option_value;

        return view("report::department.performance", compact('maxDateRange'));
    }

    /**
     * department query select
     * @return mixed
     */
    public function departmentPerformance()
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
                whereNotNull('dept_id')

            // Filtering by created at
                ->when($createStart, function ($q) use ($createStart, $createEnd) {
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

            /**
             * Abhishek added on 03-07-2018
             */
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

            /* END */

                ->select(
                    \DB::raw('count(distinct tickets.id) as assigned_tickets'), \DB::raw('SUM(CASE WHEN tickets.reopened > 0 THEN 1 ELSE 0 END) AS reopened'), \DB::raw('SUM(CASE WHEN tickets.is_response_sla = 1 THEN 1 ELSE 0 END) AS success_response_sla'), \DB::raw('SUM(CASE WHEN tickets.is_resolution_sla = 1 THEN 1 ELSE 0 END) AS success_resolution_sla'), \DB::raw('AVG(tickets.resolution_time) as avg_resolution_time'), \DB::raw('count(open_status.id) as open_tickets'), \DB::raw('count(close_status.id) as closed_tickets'), 'dept_id', 'tickets.id', 'tickets.status'
                )
                ->with(['departments' => function ($q) {
                    $q->select('id', 'name');
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
                ->groupBy('dept_id')
                ->get();
        } catch (\Exception $ex) {
            throw new Exception("Failed to generate department performance. Exception: " . $ex->getMessage());
        }

        return $this->removeAppendedColumns($ticket);
    }

    /**
     * set the condition from client
     * @param string $schema
     * @param boolean $force
     * @return object
     */
    public function addConditionToSchema($schema, $force = false)
    {
        \Log::info('Useless method in department report class');
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
                        ->join('users as agent', 'tickets.assigned_to', '=', 'agent.id')
                        ->whereIn('agent.id', $agents);
                })
                ->when($departmets, function ($query) use ($departmets) {
                    return $query
//                                ->join('department', 'tickets.dept_id', '=', 'department.id')
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
     * get the data from department performance for datatable
     * @return json
     */
    public function departmentDatatable()
    {
        $collection = $this->departmentPerformance();
        return \DataTables::of($collection)
            ->editColumn('department', function ($ticket) {
                return $ticket->departments->name;
            })
            ->addColumn('avg_response_time', function ($ticket) {
                $hours = "--";
                if ($ticket->departments->avgResponseTime()) {
                    $hours = convertToHours(abs($ticket->departments->avgResponseTime()), '%02dh %02dm');
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
                if ($ticket->departments->responses()) {
                    $responses = $ticket->departments->responses();
                }
                return $responses;
            })
            ->make(true);
    }

    /**
     * Simple function to remove unnecessary columns from ticket collection mostly unused append columns
     * @param  Collection $ticketCollection  with append columns
     * @return Collection                    without append columns
     */
    private function removeAppendedColumns($ticketsCollection)
    {
        return $ticketsCollection->each(function ($row) {
            $row->setHidden(['thread_count', 'collaborator_count', 'attachment_count', 'poster']);
        });
    }
}
