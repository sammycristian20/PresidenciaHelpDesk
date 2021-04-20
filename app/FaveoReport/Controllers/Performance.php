<?php

namespace App\FaveoReport\Controllers;

use App\FaveoReport\Controllers\ReportIndepth;
use App\Model\helpdesk\Settings\CommonSettings;

class Performance extends ReportIndepth {
    /**
     * Get performance view
     * @return view
     */
    public function getView() {
        if (!$this->policy->report()) {
            return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
        }

        $maxDateRange = (new CommonSettings)->getOptionValue('reports_max_date_range')->first()->option_value;

        return view("report::performance.distribution", compact('maxDateRange'));
    }
    /**
     * performance data
     * @return json
     */
    public function firstResponsePerformance() {
        $first_response = $this->switchActiveTicket('avg-first-response')
                        ->groupBy('ticket_thread.ticket_id')
                        ->pluck('data')
                        ->values('data')->filter()->transform(function($item, $key) {
            return (float) convertToHours($item, '%02d.%02d');
        })
        ;
        $count_collection = $this->refine($first_response);
        $labels = $count_collection->keys();
        $data = $count_collection->values();
        $bg_color = random_color();
        $label = "First response time";
        $set = ['label' => $label, 'backgroundColor' => 'rgba(75, 192, 192, 0.2)', 'borderColor' => 'rgba(75, 192, 192, 1)', 'borderWidth' => 1, 'data' => $data];
        $dataset = [$set];
        return json_encode(["chart" => ['labels' => $labels, 'datasets' => $dataset]]);
    }
    /**
     * get average response in performance
     * @return json
     */
    public function avgResponsePerformance() {
        $first_response = $this->switchActiveTicket('avg-response')
                        ->groupBy('ticket_thread.ticket_id')
                        ->pluck('data')
                        ->values('data')->filter()->transform(function($item, $key) {
            return (float) convertToHours($item, '%02d.%02d');
        })
        ;
        $count_collection = $this->refine($first_response);
        $labels = $count_collection->keys();
        $data = $count_collection->values();
        $bg_color = random_color();
        $label = "Average response time";
        $set = ['label' => $label, 'backgroundColor' => 'rgba(153, 102, 255, 0.2)', 'borderColor' => 'rgba(153, 102, 255, 0.2)', 'borderWidth' => 1, 'data' => $data];
        $dataset = [$set];
        return json_encode(["chart" => ['labels' => $labels, 'datasets' => $dataset]]);
    }
    /**
     * resolution performance
     * @return json
     */
    public function resolutionPerformance() {
        $first_response = $this->switchActiveTicket('avg-resolution')
                        ->groupBy('tickets.id')
                        ->pluck('data')
                        ->values('data')->filter()->transform(function($item, $key) {
            return (float) convertToHours($item, '%02d.%02d');
        })
        ;
        $count_collection = $this->refine($first_response);
        $labels = $count_collection->keys();
        $data = $count_collection->values();
        $bg_color = random_color();
        $label = "Average Resolution time";
        $set = ['label' => $label, 'backgroundColor' => 'rgba(255, 159, 64, 0.2)', 'borderColor' => 'rgba(255, 159, 64, 1)', 'borderWidth' => 1, 'data' => $data];
        $dataset = [$set];
        return json_encode(["chart" => ['labels' => $labels, 'datasets' => $dataset]]);
    }
    /**
     * categorise the time
     * @param decimal $first_response
     * @return colection
     */
    public function refine($first_response) {
        $result = [];
        foreach ($first_response as $hours) {
            if ($hours < 0.15) {
                $result['<15 min'][] = 1;
            } else {
                $result['<15 min'][] = 0;
            }
            if ($hours >= 0.15 && 0.30 > $hours) {
                $result['15-30 min'][] = 1;
            } else {
                $result['15-30 min'][] = 0;
            }
            if ($hours >= 0.30 && 1 > $hours) {
                $result['30-60 min'][] = 1;
            } else {
                $result['30-60 min'][] = 0;
            }
            if ($hours >= 1 && 2 > $hours) {
                $result['1-2 hrs'][] = 1;
            } else {
                $result['1-2 hrs'][] = 0;
            }
            if ($hours >= 2 && 4 > $hours) {
                $result['2-4 hrs'][] = 1;
            } else {
                $result['2-4 hrs'][] = 0;
            }
            if ($hours >= 4 && 8 > $hours) {
                $result['4-8 hrs'][] = 1;
            } else {
                $result['4-8 hrs'][] = 0;
            }
            if ($hours >= 8 && 12 > $hours) {
                $result['8-12 hrs'][] = 1;
            } else {
                $result['8-12 hrs'][] = 0;
            }
            if ($hours >= 12 && 24 > $hours) {
                $result['12-24 hrs'][] = 1;
            } else {
                $result['12-24 hrs'][] = 0;
            }
            if ($hours >= 24 && 48 > $hours) {
                $result['24-48 hrs'][] = 1;
            } else {
                $result['24-48 hrs'][] = 0;
            }
            if (48 < $hours) {
                $result['48+ hrs'][] = 1;
            } else {
                $result['48+ hrs'][] = 0;
            }
        }
        $count = collect($result)->transform(function ($item) {
            return array_sum($item);
        });
        return $count;
    }
    /**
     * get first response performance 
     * @return json
     */
    public function firstAndResponseTrend() {
        $period = $this->request->input('period', 'day');
        $createStart = $this->request->input('start_date', '');
        $createEnd = $this->request->input('end_date', '');
        $updateStart = $this->request->input('update_start');
        $updateEnd   = $this->request->input('update_end');

        $startDate = empty($createStart) ? $updateStart : $createStart;
        $endDate = empty($createEnd) ? $updateEnd : $createEnd;

        $date_range = $this->getDateRange($startDate, $endDate, $period);

        $first_response = $this->groupByPeriod('avg-first-response', $period, $date_range)->transform(function($item, $key) {
            return (float) convertToHours($item->data, '%02d.%02d');
        });
        $response = $this->groupByPeriod('avg-response', $period, $date_range)->transform(function($item, $key) {
            return (float) convertToHours($item->data, '%02d.%02d');
        });
        
        $first = [
            'label' => 'First Response Time',
            "fill" => false,
            "lineTension" => 0.1,
            "backgroundColor" => 'rgba(75, 192, 192, 0.2)',
            "borderColor" => 'rgba(75, 192, 192, 1)',
            "borderCapStyle" => 'butt',
            "borderDash" => [],
            "borderDashOffset" => 0.0,
            "borderJoinStyle" => 'miter',
            "pointBorderColor" => 'rgba(75, 192, 192, 1)',
            "pointBackgroundColor" => "#fff",
            "pointBorderWidth" => 1,
            "pointHoverRadius" => 5,
            "pointHoverBackgroundColor" => 'rgba(75, 192, 192, 1)',
            "pointHoverBorderColor" => 'rgba(75, 192, 192, 1)',
            "pointHoverBorderWidth" => 2,
            "pointRadius" => 1,
            "pointHitRadius" => 10,
            'data' => $first_response->values(),
            "spanGaps" => false,
        ];

        $responses = [
            'label' => 'Response',
            "fill" => false,
            "lineTension" => 0.1,
            "backgroundColor" => 'rgba(153, 102, 255, 0.2)',
            "borderColor" => 'rgba(153, 102, 255, 1)',
            "borderCapStyle" => 'butt',
            "borderDash" => [],
            "borderDashOffset" => 0.0,
            "borderJoinStyle" => 'miter',
            "pointBorderColor" => 'rgba(153, 102, 255, 1)',
            "pointBackgroundColor" => "#fff",
            "pointBorderWidth" => 1,
            "pointHoverRadius" => 5,
            "pointHoverBackgroundColor" => 'rgba(153, 102, 255, 1)',
            "pointHoverBorderColor" => 'rgba(153, 102, 255, 1)',
            "pointHoverBorderWidth" => 2,
            "pointRadius" => 1,
            "pointHitRadius" => 10,
            'data' => $response->values(),
            "spanGaps" => false,
        ];
        $dataset = [
            $first,
            $responses,
        ];
        $labels = $this->getDateLabels($date_range, $period);

        return response()->json(['data' => ['labels' => $labels, 'datasets' => $dataset]]);
    }
    /**
     * get the bill details
     * @param string $group
     * @return object
     */
    public function timesheet($group) {
        $schemas = $this->switchActiveTicket('created-ticket', false)
                ->join('bills', 'tickets.id', '=', 'ticket_id')
                ->join('department', 'tickets.dept_id', '=', 'department.id')
                ->join('users as client', 'tickets.user_id', '=', 'client.id')
                ->addSelect(
                        \DB::raw('SUM(bills.hours) as hours'), 
                        \DB::raw('SUM(bills.amount_hourly) as amount'), 
                        'bills.billable', 
                        'bills.note', 
                        'bills.id as billid', 
                        'bills.created_at as bill_created', 
                        'department.name as department', 
                        'client.user_name as client'
                )
                ->groupBy($group)
        ;
        $schema = $this->addConditionToSchema($schemas,true);
        return $schema;
    }
    /**
     * view for billing details
     * @return view
     */
    public function timesheetView() {
        if (!$this->policy->report()) {
            return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
        }

        $maxDateRange = (new CommonSettings)->getOptionValue('reports_max_date_range')->first()->option_value;

        return view("report::performance.timesheet", compact('maxDateRange'));
    }
    /**
     * billing details
     * @return json
     */
    public function getBillDetails() {
        $schema = $this->timesheet("bills.id");
        $array = $schema->get()->toArray();
        $bill_data['total_amount'] = collect($array)->transform(function($value){
                return $value['amount'] = $value['amount']*$value['hours'];
            })->first();
        $bill_data['total_hours'] = $schema->get()->sum('hours');
        $bill_data['bill_hour'] = $schema->get()->where('billable', '1')->sum('hours');
        $bill_data['bill_amount'] = collect($schema->get()->where('billable', '1')->toArray())->transform(function($value){
                return $value['amount'] = $value['amount']*$value['hours'];
            })->first();
        $bill_data['nonbill_amount'] = collect($schema->get()->where('billable', '0')->toArray())->transform(function($value){
                return $value['amount'] = $value['amount']*$value['hours'];
            })->first();
        $bill_data['nonbill_hour'] = $schema->get()->where('billable', '0')->sum('hours');
        return json_encode($bill_data);
    }
    /**
     * billing json response for datatable
     * @return json
     */
    public function timesheetDatatable() {
        $schema = $this->timesheet("bills.id");
        return \DataTables::of($schema)
                        ->addColumn('hours', function($bill) {
                            $billable = '<i class="fa fa-circle text-success"></i>';
                            if ($bill->billable == 0) {
                                $billable = '<i class="fa fa-circle text-danger"></i>';
                            }
                            return $bill->hours;
                        })
                        ->addColumn('amount', function($bill) {
                            return $bill->hours * $bill->amount;
                            
                        })
                        ->addColumn('bill_created', function($bill) {
                            $bill_created = carbon($bill->bill_created);
                            return $bill_created->tz(timezone())->format(dateTimeFormat());
                        })
                        ->make(true);
    }
    /**
     * export the details
     * @param type $storage
     * @param type $mail
     * @return string
     */
    public function exportTimesheet($storage = true, $mail = false) {
        $storage_path = storage_path('exports');
        if (is_dir($storage_path)) {
            delTree($storage_path);
        }
        $tickets = $this->timesheet("bills.id")->get()->toArray();
        if (count($tickets) > 0) {
            $filename = "Time_sheet";
            $excel = \Excel::create($filename, function($excel) use($tickets) {
                        $excel->sheet('sheet', function($sheet) use($tickets) {
                            $sheet->fromArray($tickets);
                        });
                    });
            if ($storage == false) {
                $excel->download("csv");
            } else {
                $path = $excel->store('csv', false, true);
            }
        } else {
            return 'No data to export';
        }
        if ($mail == true) {
            return $path;
        }
        return 'success';
    }
    /**
     * send report mail
     * @return json
     */
    public function mail() {

        $this->validate($this->request, [
            'send_agents' => 'required',
            'subject' => 'max:20',
        ]);
        try {
            $path = $this->exportTimesheet(true, true);
            $this->mailing($path, 'timesheet');
            $message = "Mail has sent";
            $status_code = 200;
        } catch (\Exception $ex) {
            $message = [$ex->getMessage()];
            $status_code = 500;
        }
        return $this->mailResponse($message, $status_code);
    }

}
