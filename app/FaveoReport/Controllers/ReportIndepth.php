<?php

namespace App\FaveoReport\Controllers;

use App\FaveoReport\Controllers\ReportController;
use App\Model\helpdesk\Settings\CommonSettings;
use Carbon\Carbon;

class ReportIndepth extends ReportController
{
    /**
     * get the indepth view
     * @return view
     */
    public function getView()
    {
        if (!$this->policy->report()) {
            return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
        }

        $controller   = $this;
        $maxDateRange = (new CommonSettings)->getOptionValue('reports_max_date_range')->first()->option_value;

        return view('report::indepth.indepth', compact('controller', 'maxDateRange'));
    }

    /**
     * get the ticket schema
     * @param string $active
     * @param string $group
     * @param boolean $count
     * @return object
     */
    public function _tickets($active, $group = "", $count = true, $value = false)
    {
        $schema = $this->switchActiveTicket($active, $count);
        if ($value) {
            return $schema->first();
        }
        $optimize_schema = $this->optimizeSelect($schema, $group);
        $tickets         = $this->groupBy($optimize_schema, $active, $group);
        return $tickets;
    }

    /**
     *
     * @param object $schema
     * @param string $group
     * @return mixed
     */
    public function optimizeSelect($schema, $group = "")
    {

        switch ($group) {
            case "source":
                return $schema
                    ->leftJoin('ticket_source', 'tickets.source', '=', 'ticket_source.id')
                    ->addSelect('ticket_source.name as source');
            case "status":
                return $schema
                    ->addSelect('ticket_status.name as status');
            case "type":
                return $schema
                    ->join('ticket_type as type', 'tickets.type', '=', 'type.id')
                    ->addSelect('type.name as type');
            case "priority":
                return $schema
                    ->leftJoin('ticket_priority', 'tickets.priority_id', '=', 'ticket_priority.priority_id')
                    ->addSelect('ticket_priority.priority');
            default:
                return $schema
                    ->addSelect('ticket_status.name as status');
        }
    }

    /**
     *
     * @param mixed $schema
     * @param string $active
     * @param string $group
     * @return mixed
     */
    public function groupBy($schema, $active, $group = "")
    {
        if (!$group && $active === "first-contact-resolution") {
            $collection = $schema->get();
        }
        if ($group) {
            $collection = $schema->groupBy($group)->get();
        }

        if (!$group) {
            $collection = $schema->get();
        }

        return $collection;
    }

    /**
     *
     * @param string $active
     * @param boolean $count
     * @param boolean $force
     * @return mixed
     */
    public function switchActiveTicket($active, $count = true, $force = false)
    {
        if ($count == true) {
            $select = \DB::raw('COUNT(tickets.id) as data');
        } else {
            $select = "tickets.id";
        }
        switch ($active) {
            case "created-ticket":
                $schema = $this->tickets()
                    ->select(
                        $select, 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d %h:00:00') as hour"), \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date")
                    );
                break;
            case "resolved-ticket":
                $schema = $this->tickets()
                    ->where('ticket_status_type.name', '=', 'closed')
                    ->select(
                        $select, 'tickets.created_at as created', 'tickets.closed_at as resolved', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d %h:00:00') as hour"), \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date"), \DB::raw("DATE_FORMAT(tickets.closed_at, '%Y-%m-%d') as resolved_at")
                    );

                break;
            case "unresolved-ticket":
                $schema = $this->tickets()
                    ->where('ticket_status_type.name', '!=', 'closed')
                    ->select(
                        \DB::raw('COUNT(tickets.id) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date")
                    );
                break;
            case "non-closed":
                $schema = $this->tickets()
                    ->where('ticket_status_type.name', '!=', 'closed')
                    ->select(
                        \DB::raw('COUNT(tickets.id) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date"), \DB::raw("DATE_FORMAT(tickets.created_at, '%l') as day"), \DB::raw("DATE_FORMAT(tickets.closed_at, '%Y-%m-%d') as resolved_at")
                    );
                break;
            case "reopened-ticket":
                $schema = $this->tickets()
                //->where('ticket_status_type.name', '=', 'open')
                    ->where('tickets.reopened', '>', '0')
                    ->select(
                        \DB::raw('COUNT(tickets.id) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date")
                    );
                break;
            case "avg-first-response":
                $schema = $this->tickets()
                    ->join('ticket_thread', 'tickets.id', '=', 'ticket_thread.ticket_id')
                    ->where('ticket_thread.thread_type', '=', 'first_reply')
                    ->select(
                        \DB::raw('COUNT(tickets.id) as tickets'), \DB::raw('AVG(ticket_thread.response_time) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date")
                    );
                break;
            case "avg-response":
                $schema = $this->tickets()
                    ->leftJoin('ticket_thread', 'tickets.id', '=', 'ticket_thread.ticket_id')
                    ->select(
                        \DB::raw('AVG(ticket_thread.response_time) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date"));
                break;

            case "agent-responses":
                $schema = $this->tickets()
                    ->join('ticket_thread', 'tickets.id', '=', 'ticket_thread.ticket_id')
                    ->where('ticket_thread.poster', '=', 'support')
                    ->where('ticket_thread.is_internal', '=', 0)
                    ->select(
                        \DB::raw('COUNT(ticket_thread.id) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date"));
                break;

            case "client-responses":
                $schema = $this->tickets()
                    ->join('ticket_thread', 'tickets.id', '=', 'ticket_thread.ticket_id')
                    ->where('ticket_thread.poster', '=', 'client')
                    ->where('ticket_thread.is_internal', '=', 0)
                    ->select(
                        \DB::raw('COUNT(ticket_thread.id) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date"));
                break;

            case "avg-resolution":
                $schema = $this->tickets()
                    ->select(
                        \DB::raw('AVG(tickets.resolution_time) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date")
                    );
                break;
            case "response-sla":
                $schema = $this->tickets()
                    ->select(
                        \DB::raw('SUM(tickets.is_response_sla) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date")
                    );
                break;
            case "resolve-sla":
                $schema = $this->tickets()
                    ->select(
                        \DB::raw('SUM(tickets.is_resolution_sla) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date"));
                break;

            case "first-contact-resolution":
                $schema = $this->tickets()
                    ->where('ticket_status_type.name', '=', 'closed')
                    ->join('ticket_thread', function ($join) {
                        $join->on('tickets.id', '=', 'ticket_thread.ticket_id')
                            ->where('ticket_thread.is_internal', "=", 0)
                            ->where('ticket_thread.poster', "=", 'support');
                    })
                    ->havingRaw('COUNT(ticket_thread.id) < 2')
                    ->select(
                        \DB::raw('COUNT(distinct ticket_thread.id) as threads'), 'tickets.id as ticket_id', \DB::raw('COUNT(distinct tickets.id) as data'), 'tickets.created_at as created', \DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date")
                    )

                ;
                break;
                //return $schama;
        }
        return $this->addConditionToSchema($schema, $force);
    }

    /**
     * set the conditions
     * @param mixed $schema
     * @param boolean $force
     * @return mixed
     */
    public function addConditionToSchema($schema, $force = false)
    {
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

        if ($force != true) {
            $schema = $schema
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
                ->when($agents, function ($query) use ($agents) {
                    return $query
                        ->join('users as agent', 'tickets.assigned_to', '=', 'agent.id')
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
                    return $query->whereIn('tickets.source', $source);
                })
                ->when($priority, function ($query) use ($priority) {
                    return $query->whereIn('tickets.priority_id', $priority);
                })
                ->when($type, function ($query) use ($type) {
                    return $query->whereIn('tickets.type', $type);
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
                });
        }

        return $schema;
    }

    /**
     * get the tickets details
     * @return json
     */
    public function getTickets()
    {
        $active       = $this->request->input('active', 'response-sla');
        $group        = $this->request->input('group', 'source');
        $tickets      = $this->_tickets($active, $group);
        $labels       = $tickets->pluck($group)->toArray();
        $data         = $this->getData($tickets, $active)->toArray();
        $count_ticket = $this->_tickets($active, $group, true, true);
        $bg_color     = $this->bgColor(count($data));
        $label        = \Lang::get('report::lang.split-by') . " " . ucfirst($group);
        $set          = ['label' => $label, 'backgroundColor' => $bg_color, 'borderColor' => $bg_color, 'borderWidth' => 1, 'data' => $data];
        $dataset      = [$set];
        $count        = $this->getTotalCount($active, $count_ticket);
        return json_encode(["chart" => ['labels' => $labels, 'datasets' => $dataset], "count" => $count]);
    }

    /**
     * get the colors
     * @param integer $count
     * @return string
     */
    public function bgColor($count)
    {
        $color  = [];
        $colors = '#dd4b38';
        for ($i = 0; $i < $count; $i++) {
            $color[] = $colors;
        }
        return $color;
    }

    /**
     * get the dataof team performance
     * @param mixed $tickets
     * @param string $active
     * @param string $format
     * @return mixed
     */
    public function getData($tickets, $active, $format = '%02d.%02d')
    {
        $data = $tickets->pluck('data');
        switch ($active) {
            case "avg-first-response":
                return $data->transform(function ($item) use ($format) {
                    return convertToHours($item, $format);
                });
            case "avg-response":
                return $data->transform(function ($item) use ($format) {
                    return convertToHours($item, $format);
                });
            case "avg-resolution":
                return $data->transform(function ($item) use ($format) {
                    return convertToHours($item, $format);
                });
            default:
                return $data;
        }
    }

    /**
     * get the total count of the data
     * @param string $active
     * @param mixed $tickets
     * @return mixed
     */
    public function getTotalCount($active, $tickets, $format = '%02d.%02d')
    {
        $data = ($tickets) ? $tickets->data : 0;
        switch ($active) {
            case "avg-first-response":
                return convertToHours($data, $format);
            case "avg-response":
                return convertToHours($data, $format);
            case "avg-resolution":
                return convertToHours($data, $format);
            default:
                return $data;
        }
    }

    /**
     * get the view of trend
     * @return view
     */
    public function trends()
    {
        if (!$this->policy->report()) {
            return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
        }

        $controller = $this;
        $maxDateRange = (new CommonSettings)->getOptionValue('reports_max_date_range')->first()->option_value;

        return view('report::indepth.trends', compact('controller', 'maxDateRange'));
    }

    /**
     * data of trend
     * @return json
     */
    public function getTrends()
    {
        $period      = $this->request->input('period', 'day');
        $createStart = $this->request->input('start_date');
        $endDate     = $this->request->input('end_date');
        $updateStart = $this->request->input('update_start');
        $updateEnd   = $this->request->input('update_end');

        $startDate = empty($createStart) ? $updateStart : $createStart;
        $endDate   = empty($createEnd) ? $updateEnd : $createEnd;

        $date_range = $this->getDateRange($startDate, $endDate, $period);

        $recieved   = $this->groupByPeriod('created-ticket', $period, $date_range);
        $resolved   = $this->groupByPeriod('resolved-ticket', $period, $date_range);
        $unresolved = $this->groupByPeriod('non-closed', $period, $date_range);

        $counts = [
            'recieved_total'   => $recieved->sum('data'),
            'resolved_total'   => $resolved->sum('data'),
            'unresolved_total' => $unresolved->sum('data'),
            'recieved_avg'     => (int) $recieved->avg('data'),
            'resolved_avg'     => (int) $resolved->avg('data'),
            'unresolved_avg'   => (int) $unresolved->avg('data'),
        ];

        $label_created    = "Received";
        $label_resolved   = "Resolved";
        $label_unresolved = "Unresolved";
        $data_resolved    = $this->convertData($date_range, $resolved, 'resolved');
        $data_unresolved  = $this->getUnresolved($date_range, $unresolved, $period);
        $data_created     = $this->convertData($date_range, $recieved);

        $set_created    = ['label' => $label_created, 'backgroundColor' => 'rgba(255, 99, 132, 0.2)', 'borderColor' => 'rgba(255,99,132,1)', 'borderWidth' => 1, 'data' => $data_created];
        $set_resolved   = ['label' => $label_resolved, 'backgroundColor' => 'rgba(54, 162, 235, 0.2)', 'borderColor' => 'rgba(54, 162, 235, 1)', 'borderWidth' => 1, 'data' => $data_resolved];
        $set_unresolved = ['label' => $label_unresolved, 'backgroundColor' => 'rgba(255, 206, 86, 0.2)', 'borderColor' => 'rgba(255, 206, 86, 1)', 'borderWidth' => 1, 'data' => $data_unresolved];

        $dataset = [
            $set_created,
            $set_resolved,
            $set_unresolved,
        ];
        $labels = $this->getDateLabels($date_range, $period);

        return response()->json(['data' => ['labels' => $labels, 'datasets' => $dataset], 'count' => $counts]);
    }

    /**
     * grouping the data
     * @param type $active
     * @param type $period
     * @param type $labels
     * @return collection
     */
    public function groupByPeriod($active = 'created-ticket', $period = 'week', $labels)
    {
        $active_schema   = $this->switchActiveTicket($active);
        $optimize_schema = $this->optimizeSelect($active_schema);
        $date_first      = Carbon::parse(array_first($labels));
        $date_last       = Carbon::parse(array_last($labels))->endOfDay();

        if ($period === 'day' || $period === 'week') {
            $result = $optimize_schema
                ->whereBetween('tickets.created_at', [$date_first, $date_last])
                ->groupBy('date')
                ->get();
        } elseif ($period === 'month') {
            $result = $optimize_schema
                ->addSelect(\DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m') as month"))
                ->whereBetween('tickets.created_at', [$date_first, $date_last])
                ->groupBy('month')
                ->get();
        } else {
            $result = $optimize_schema
                ->addSelect(\DB::raw("DATE_FORMAT(tickets.created_at, '%Y') as year"))
                ->whereYear('tickets.created_at', '<=', $date_last)
                ->groupBy('year')
                ->get();
        }
        return collect($result);
    }

    /**
     * labeling the array
     * @param type $period
     * @return array
     */
    public function getDateRange($start, $end, $period = 'day')
    {
        $end = empty($end) ? Carbon::today() : Carbon::parse($end);

        $lables = array();

        switch ($period) {
            case 'day':
                $start = empty($start) ? (clone $end)->subMonth()->addDay() : Carbon::parse($start);
                for ($date = $start; $date->lte($end); $date->addDay()) {
                    $lables[] = $date->format('Y-m-d');
                }
                break;
            case 'week':
                $start = empty($start) ? (clone $end)->subMonth()->addDay() : Carbon::parse($start);
                for ($date = $start; $date->lte($end); $date->addWeek()) {
                    $lables[] = $date->format('Y-m-d');
                }
                break;
            case 'month':
                $start = empty($start) ? (clone $end)->subMonth(6)->startOfMonth() :
                Carbon::parse($start)->startOfMonth();
                $end->endOfMonth();
                for ($date = $start; $date->lte($end); $date->addMonth()) {
                    $lables[] = $date->format('Y-m-d');
                }
                break;
            case 'year':
                $start = empty($start) ? (clone $end)->subYear(5)->addDay() : Carbon::parse($start);
                for ($date = $start; $date->lte($end); $date->addYear()) {
                    $lables[] = $date->format('Y');
                }
                break;
        }

        return $lables;
    }

    /**
     * Get date labels in proper date format
     *
     * @param $dates Array Array of dates
     * @param $period String Day, week, month, year. Default day.
     * @return Array Array of formated dates
     */
    public function getDateLabels(array $dates, $period = 'day')
    {
        return array_map(function ($date) use ($period) {
            switch ($period) {
                case 'day':
                case 'week':
                    return Carbon::parse($date)->format('j M');
                    break;
                case 'month':
                    return Carbon::parse($date)->format('F');
                    break;
                case 'year':
                    return $date;
                    break;
            }
        }, $dates);
    }

    /**
     * get the trend data
     * @return json
     */
    public function getTrendsLoad()
    {
        $period      = $this->request->input('period', 'month');
        $createStart = $this->request->input('start_date');
        $endDate     = $this->request->input('end_date');
        $updateStart = $this->request->input('update_start');
        $updateEnd   = $this->request->input('update_end');

        $startDate = empty($createStart) ? $updateStart : $createStart;
        $endDate   = empty($createEnd) ? $updateEnd : $createEnd;

        $date_range = $this->getDateRange($startDate, $endDate, $period);

        $recieved   = $this->groupByPeriod('created-ticket', $period, $date_range);
        $resolved   = $this->groupByPeriod('resolved-ticket', $period, $date_range);
        $unresolved = $this->groupByPeriod('non-closed', $period, $date_range);

        $group = $period === 'year' ? $period : 'date';

        $data_resolved    = $this->convertData($date_range, $resolved, 'resolved');
        $data_unresolved  = $this->getUnresolved($date_range, $unresolved, $period);
        $label_resolved   = "Resolved";
        $label_unresolved = "Unresolved";
        $set_resolved     = ['label' => $label_resolved, 'backgroundColor' => 'rgba(54, 162, 235, 0.2)', 'borderColor' => 'rgba(54, 162, 235, 1)', 'borderWidth' => 1, 'data' => $data_resolved];
        $set_unresolved   = ['label' => $label_unresolved, 'backgroundColor' => 'rgba(255, 206, 86, 0.2)', 'borderColor' => 'rgba(255, 206, 86, 1)', 'borderWidth' => 1, 'data' => $data_unresolved];

        $dataset = [
            $set_resolved,
            $set_unresolved,
        ];
        $labels = $this->getDateLabels($date_range, $period);

        return response()->json(['labels' => $labels, 'datasets' => $dataset]);
    }

    /**
     * get the non-resolved ticket count
     * @param type $date
     * @param type $unresolved
     * @param type $period
     * @return integer
     */
    public function nonResolved($date, $unresolved, $period)
    {
        $ticket_count = $unresolved
            ->filter(function ($item, $key) use ($date, $period) {
                if ($period !== 'year') {
                    return $item->date <= $date;
                } else {
                    return $item->year <= $date;
                }
            })
            ->sum('data')

        ;
        return $ticket_count;
    }

    /**
     * get the ticket table
     * @param type $active
     * @param type $date
     */
    public function getTicketTable($active, $date)
    {
        $ticket_count = $this->switchActiveTicket($active)
            ->whereDate('tickets.created_at', $date)
            ->get()
        ;
    }

    /**
     * get un-resolved ticket details
     * @param type $labels
     * @param type $unresolved
     * @param type $period
     * @return array
     */
    protected function getUnresolved($labels, $unresolved, $period)
    {
        $count = [];
        foreach ($labels as $date) {
            $count[$date] = $this->nonResolved($date, $unresolved, $period);
        }
        ksort($count);
        return collect($count)->values()->toArray();
    }

    /**
     * transform the data without null values
     * @param type $labels
     * @param type $collection
     * @return array
     */
    protected function convertData($labels, $collection, $type = 'created')
    {
        $date_field = $type == 'created' ? 'date' : 'resolved_at';

        $ticket_counts = $collection->pluck('data', $date_field);

        $labels = array_map(function ($item) use ($ticket_counts) {
            return $ticket_counts->has($item) ? $ticket_counts->get($item) : 0;
        }, $labels);

        return $labels;
    }

    /**
     * get the day trend values
     * @param type $group
     * @return array
     */
    public function dayTrends($group = 'recieved')
    {
        $recieved           = $this->_tickets('created-ticket', 'date')->pluck('date', 'data');
        $resolved           = $this->_tickets('resolved-ticket', 'resolved_at')->pluck('resolved_at', 'data');
        $recieved_transform = $this->transformData($recieved)->flip();
        $resolved_transform = $this->transformData($resolved)->flip();

        $recieved_array = $this->getDaysData($recieved_transform);
        $resolved_array = $this->getDaysData($resolved_transform);
        $array          = $recieved_array;
        if ($group == 'resolved') {
            $array = $resolved_array;
        }
        return $array;
    }

    /**
     * hours treand data
     * @param type $group
     * @param type $day
     * @return array
     */
    public function hoursTrends($group = 'recieved', $day = "")
    {
        $recieved           = $this->_tickets('created-ticket', 'hour')->pluck('hour', 'data');
        $resolved           = $this->_tickets('resolved-ticket', 'hour')->pluck('hour', 'data');
        $recieved_transform = $this->transformData($recieved, 'l:h')->flip();
        $resolved_transform = $this->transformData($resolved, 'l:h')->flip();
        $recieved_hours     = $this->getHourArray($recieved_transform);
        $resolved_hours     = $this->getHourArray($resolved_transform);
        $received_days      = $this->setTheDays($recieved_hours);
        $resolved_days      = $this->setTheDays($resolved_hours);
        if ($group == 'recieved' && $day != "") {
            $array = $received_days->get($day);
        }
        if ($group != 'recieved' && $day != "") {
            $array = $resolved_days->get($day);
        }
        if ($group == 'recieved' && $day == "") {
            $array = $received_days;
        }
        if ($group != 'recieved' && $day == "") {
            $array = $resolved_days;
        }
        return $array;
    }

    /**
     * set the days of the trend
     * @param type $collection
     * @return collection
     */
    public function setTheDays($collection)
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach ($days as $day) {
            if (array_key_exists($day, $collection->toArray())) {
                $array[$day] = $collection[$day];
            } else {
                $array[$day] = [];
            }
        }
        $result = $this->addKeys(collect($array));
        return collect($result);
    }

    /**
     * add keys to results
     * @param collection $collection
     * @return array
     */
    public function addKeys($collection)
    {
        $hours_array = collect([0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0, 16 => 0, 17 => 0, 18 => 0, 19 => 0, 20 => 0, 21 => 0, 22 => 0, 23 => 0]);
        $days        = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $week        = [];
        foreach ($collection as $day => $array) {
            if (count($array) > 0) {
                foreach (collapse($array) as $key => $value) {
                    $week[$day][$key] = $value;
                }
            }
        }
        foreach ($days as $day) {
            if (array_key_exists($day, $week)) {
                $union[$day] = collect($week[$day])->union($hours_array)->toArray();
            } else {
                $union[$day] = $hours_array->toArray();
            }
            ksort($union[$day]);
        }
        return $union;
    }

    /**
     * get the hours from the array
     * @param collection $collection
     * @return collection
     */
    public function getHourArray($collection)
    {
        $array = [];
        foreach ($collection as $key => $rec) {
            $array[substr($key, 0, 3)][] = [(int) substr($key, -2) => $rec];
        }
        return collect($array);
    }

    /**
     * get the day data
     * @param collection $collection
     * @return collection
     */
    public function getDaysData($collection)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            if ($collection->has($day)) {
                $array[$day] = $collection[$day];
            } else {
                $array[$day] = 0;
            }
        }

        return collect($array);
    }

    /**
     * transform the data to carbon format
     * @param collection $collection
     * @param string $format
     * @return collection
     */
    public function transformData($collection, $format = "l")
    {
        $collection->transform(function ($key, $value) use ($format) {
            return carbon($key)->format($format);
        });

        return $collection;
    }

    /**
     * explode the collection value of days
     * @param collection $collection
     * @param boolean $max
     * @return collection
     */
    public function explodeDays($collection, $max = true)
    {
        foreach ($collection as $key => $array) {
            $count[$key] = array_sum($array);
        }
        if ($max == true) {
            return array_keys($count, max($count))[0];
        }
        return collect($count);
    }

    /**
     * explode the collection value of hours
     * @param collection $collection
     * @param boolean $max
     * @return collection
     */
    public function explodeHours($collection, $max = true)
    {
        foreach ($collection as $key => $array) {
            foreach ($array as $k => $v) {
                $count[$k][] = $v;
            }
        }
        foreach ($count as $key => $value) {
            $result[$key] = array_sum($value);
        }
        if ($max == true) {
            $max      = array_keys($result, max($result))[0];
            $max_next = $max + 1;
            return "$max - $max_next";
        }
        return collect($result);
    }

    /**
     * get hours data
     * @return json
     */
    public function hour()
    {
        $day                  = $this->request->input('day');
        $hours                = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];
        $bg_color_recieced    = random_color();
        $bg_color_resolved    = random_color();
        $recieved_count       = $this->hoursTrends('recieved');
        $resolved_count       = $this->hoursTrends('resolved');
        $recived_count_hours  = $this->explodeHours($recieved_count);
        $resolved_count_hours = $this->explodeHours($resolved_count);
        $recived_count_days   = $this->explodeDays($recieved_count);
        $resolved_count_days  = $this->explodeDays($resolved_count);
        $details              = ['recived_max_hours' => $recived_count_hours, 'resolved_max_hours'                          => $resolved_count_hours,
            'recived_max_days'                           => \Lang::get("report::lang.$recived_count_days"), 'resolved_max_days' => \Lang::get("report::lang.$resolved_count_days")];
        $recieved = [
            'label'                     => 'Recieved',
            "fill"                      => false,
            "lineTension"               => 0.1,
            "backgroundColor"           => 'rgba(255, 99, 132, 0.2)',
            "borderColor"               => 'rgba(255,99,132,1)',
            "borderCapStyle"            => 'butt',
            "borderDash"                => [],
            "borderDashOffset"          => 0.0,
            "borderJoinStyle"           => 'miter',
            "pointBorderColor"          => 'rgba(255,99,132,1)',
            "pointBackgroundColor"      => "#fff",
            "pointBorderWidth"          => 1,
            "pointHoverRadius"          => 5,
            "pointHoverBackgroundColor" => 'rgba(255,99,132,1)',
            "pointHoverBorderColor"     => 'rgba(255,99,132,1)',
            "pointHoverBorderWidth"     => 2,
            "pointRadius"               => 1,
            "pointHitRadius"            => 10,
            'data'                      => collect($this->hoursTrends('recieved', $day))->values(),
            "spanGaps"                  => false,
        ];

        $resolved = [
            'label'                     => 'Resolved',
            "fill"                      => false,
            "lineTension"               => 0.1,
            "backgroundColor"           => 'rgba(54, 162, 235, 0.2)',
            "borderColor"               => 'rgba(54, 162, 235, 1)',
            "borderCapStyle"            => 'butt',
            "borderDash"                => [],
            "borderDashOffset"          => 0.0,
            "borderJoinStyle"           => 'miter',
            "pointBorderColor"          => 'rgba(54, 162, 235, 1)',
            "pointBackgroundColor"      => "#fff",
            "pointBorderWidth"          => 1,
            "pointHoverRadius"          => 5,
            "pointHoverBackgroundColor" => 'rgba(54, 162, 235, 1)',
            "pointHoverBorderColor"     => 'rgba(54, 162, 235, 1)',
            "pointHoverBorderWidth"     => 2,
            "pointRadius"               => 1,
            "pointHitRadius"            => 10,
            'data'                      => collect($this->hoursTrends('resolved', $day))->values(),
            "spanGaps"                  => false,
        ];

        $option = $this->hourChartOptions($day);

        $dataset = [
            $recieved,
            $resolved,
        ];
        return json_encode(['data' => ['labels' => $hours, 'datasets' => $dataset], 'option' => $option, 'count' => $details]);
    }

    /**
     * get the day vise trend
     * @param string $day
     * @return array
     */
    public function hourChartOptions($day)
    {
        switch ($day) {
            case 'Mon':
                return ['title' => ["display" => true, "text" => "Monday Trends"]];
            case 'Tue':
                return ['title' => ["display" => true, "text" => "Tuesday Trends"]];
            case 'Wed':
                return ['title' => ["display" => true, "text" => "Wednesday Trends"]];
            case 'Thu':
                return ['title' => ["display" => true, "text" => "Thursday Trends"]];
            case 'Fri':
                return ['title' => ["display" => true, "text" => "Friday Trends"]];
            case 'Sat':
                return ['title' => ["display" => true, "text" => "Saturday Trends"]];
            case 'Sun':
                return ['title' => ["display" => true, "text" => "Sunday Trends"]];
        }
    }

    /**
     * get organization view
     * @return view
     */
    public function orgView()
    {
        if (!$this->policy->report()) {
            return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
        }

        $maxDateRange = (new CommonSettings)->getOptionValue('reports_max_date_range')->first()->option_value;

        return view("report::indepth.org", compact('maxDateRange'));
    }

    /**
     * get organization data
     * @return json
     */
    public function getOrg()
    {
        $active     = $this->request->input('active', 'created-ticket');
        $sort       = $this->request->input('sort', 'desc');
        $percentage = $this->request->input('percentage', 'false');
        $schema     = $this->switchActiveTicket($active)
            ->join('users as client', 'tickets.user_id', '=', 'client.id')
            ->join('user_assign_organization', 'client.id', '=', 'user_assign_organization.user_id')
            ->join('organization', 'user_assign_organization.org_id', '=', 'organization.id')
            ->addSelect('organization.name as org')
            ->groupBy('organization.id')
            ->orderBy('data', $sort)
            ->take(5)
            ->pluck('data', 'org')
        ;
        if ($percentage == 'true') {
            $total = $schema->sum();
            $schema->transform(function ($item, $key) use ($total) {
                $percent = 0;
                if ($item && $total) {
                    $percent = ($item / $total) * 100;
                }
                return $schema[$key] = round($percent, 2);
            });
        }
        $labels  = $schema->keys();
        $label   = $this->labelsByTickets($active);
        $data    = $schema->values();
        $set     = ['label' => $label, 'backgroundColor' => 'rgba(76, 192, 192, 1)', 'borderColor' => 'rgba(75, 192, 192, 1)', 'borderWidth' => 1, 'data' => $data];
        $dataset = [$set];
        return json_encode(["chart" => ['labels' => $labels, 'datasets' => $dataset]]);
    }

    /**
     * get the labels for the graph
     * @param string $active
     * @return string
     */
    public function labelsByTickets($active)
    {
        switch ($active) {
            case "created-ticket":
                $lable = trans('report::lang.recieved-tickets');
                break;
            case "resolved-ticket":
                $lable = trans('report::lang.resolved-tickets');
                break;
            case "unresolved-ticket":
                $lable = trans('report::lang.unresolved-tickets');
                break;
            case "non-closed":
                $lable = trans('report::lang.open-tickets');
                break;
            case "reopened-ticket":
                $lable = trans('report::lang.reopened-tickets');
                break;
            case "avg-first-response":
                $lable = trans('report::lang.avg-first-response');
                break;
            case "avg-response":
                $lable = trans('report::lang.avg-response');
                break;
            case "avg-resolution":
                $lable = trans('report::lang.avg-resolution');
                break;
            case "response-sla":
                $lable = trans('report::lang.response-sla');
                break;
            case "resolve-sla":
                $lable = trans('report::lang.resolve-sla');
                break;
            case "first-contact-resolution":
                $lable = trans('report::lang.first-contact-resolution');
                break;
            case "agent-responses":
                $lable = trans('report::lang.agent-responses');
                break;
            case "client-responses":
                $lable = trans('report::lang.client-responses');
                break;
        }
        return $lable;
    }

    /**
     * get the customer satisfaction report view
     * @return view
     */
    public function satisfactionView()
    {
        if (!$this->policy->report()) {
            return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
        }

        $maxDateRange = (new CommonSettings)->getOptionValue('reports_max_date_range')->first()->option_value;

        return view('report::performance.satisfaction', compact('maxDateRange'));
    }

    /**
     * get the satisfaction data
     * @return json
     */
    public function getSatisfaction()
    {
        $active      = $this->request->input('active', 'created-ticket');
        $rating_type = $this->request->input('rating_type', 'ticket');

        if ($rating_type == 'thread') {
            $schema = $this->switchActiveTicket($active)
                ->join('rating_ref', 'tickets.id', '=', 'rating_ref.ticket_id')
                ->where('rating_ref.thread_id', '!=', 0)
                ->addSelect('rating_ref.rating_value as rating', \DB::raw('count(rating_ref.thread_id) as rating_count'))
                ->groupBy('rating_ref.rating_value')
                ->pluck('rating_count', 'rating');
        } else {
            $schema = $this->switchActiveTicket($active)
                ->join('rating_ref', 'tickets.id', '=', 'rating_ref.ticket_id')
                ->where('rating_ref.thread_id', '=', 0)
                ->addSelect('rating_ref.rating_value as rating', \DB::raw('count(rating_ref.ticket_id) as rating_count'))
                ->groupBy('rating_ref.rating_value')
                ->pluck('rating_count', 'rating');
        }

        $rating = $schema->union([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0])->toArray();

        ksort($rating);
        $collection = collect($rating);
        $labels     = $collection->keys();
        $data       = $collection->values();

        $set     = ['label' => "Rating", 'backgroundColor' => 'rgba(76, 192, 192, 1)', 'borderColor' => 'rgba(76, 192, 192, 1)', 'borderWidth' => 1, 'data' => $data];
        $dataset = [$set];
        return json_encode(['labels' => $labels, 'datasets' => $dataset]);
    }
}
