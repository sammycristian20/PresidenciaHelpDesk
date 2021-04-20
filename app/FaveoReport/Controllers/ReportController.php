<?php

namespace App\FaveoReport\Controllers;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Ticket\Tickets;
use DB;
use Illuminate\Http\Request;
use File;
use Exception;
use Lang;
use App\User;

/**
 * Report Controller
 * 
 * @abstract Controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name ReportController
 * 
 */
class ReportController extends Controller {

    protected $request;

    public function __construct(Request $req) {
        $this->middleware(['auth']);
        $this->request = $req;
    }

    /**
     * 
     * @return typeget the joined ticket
     */
    public function tickets() {
        $tickets = new Tickets();
        return $tickets->leftJoin('ticket_status', 'tickets.status', '=', 'ticket_status.id')
                    ->leftJoin('ticket_status_type', 'ticket_status.purpose_of_status', '=', 'ticket_status_type.id');
    }

    /**
     * get the doughnut chart
     * 
     * @return json
     */
    public function doughnut($tickets) {
        $array = [];
        $labels = [];
        $data = [];
        $color = [];
        foreach ($tickets as $ticket) {
            $data[] = (int) $ticket->tickets;
            $color[] = random_color();
            $labels[] = $ticket->name;
        }
        $dataset = ['data' => $data, 'backgroundColor' => $color, 'hoverBackgroundColor' => $color];
        $array = ['labels' => $labels, 'datasets' => [$dataset]];
        return json_encode($array);
    }

    /**
     * 
     * Get the line chart
     * 
     * @param object $tickets
     * @param string $category
     * @return json
     */
    public function line($tickets, $category) {
        $labels = [];
        if (!$category) {
            $category = "month";
        }
        $labels = $this->labelsFormat($category);
        $tickets = $this->group($tickets, $category);
        //dd($tickets);
        $color = [];
        $dataset = [];
        $statuses_array = \App\Model\helpdesk\Ticket\Ticket_Status::pluck('name');
        foreach ($statuses_array as $status) {
//            echo json_encode($this->filter($tickets, $status))."<br>";
            $color = random_color();
            $dataset[] = [
                "fill" => false,
                "lineTension" => 0.1,
                "borderCapStyle" => 'butt',
                "borderDash" => [],
                "borderDashOffset" => 0.0,
                "borderJoinStyle" => 'miter',
                "pointBorderWidth" => 1,
                "pointHoverRadius" => 5,
                "pointHoverBorderWidth" => 2,
                "pointRadius" => 1,
                "pointHitRadius" => 10,
                "spanGaps" => true,
                "label" => $status,
                "borderColor" => $color,
                "backgroundColor" => $color,
                "pointBorderColor" => $color,
                "pointBackgroundColor" => $color,
                "pointHoverBackgroundColor" => $color,
                "pointHoverBorderColor" => $color,
                "data" => $this->filter($tickets, $status)
            ];
        }
        //dd('yes');
        $array = ['labels' => $labels, 'datasets' => $dataset];
        return json_encode($array);
    }

    /**
     * 
     * @param string $category
     * @param string $format
     * @return object carbon
     */
    public function labelsForLine($category = "month", $format = "Y-m-d") {

        switch ($category) {
            case "week":
                $start_date = \Carbon\Carbon::now()->subDays(7)->tz(timezone());
                return $this->generateDateRange($start_date, $category, $format);
             case "today":
                $start_date = \Carbon\Carbon::now()->subHours(24)->tz(timezone());
                return $this->generateDateRange($start_date, $category, $format);
            case "month":
                $start_date = \Carbon\Carbon::now()->subMonth()->tz(timezone());
                return $this->generateDateRange($start_date, $category, $format);
            case "year":
                $start_date = \Carbon\Carbon::now()->subYear()->tz(timezone());
//                dd($this->generateDateRange($start_date,$format));
                return $this->generateDateRange($start_date, $category, $format);
            case "custom":
                $start_date = $this->getCarbon($this->request->input('start'))->tz(timezone());
                return $this->generateDateRange($start_date, $category, $format);
        }
    }

    /**
     * 
     * get label for line chart
     * 
     * @param string $category
     * @return array
     */
    public function labelsFormat($category = "month") {

        switch ($category) {
            case "today":
                return $this->labelsForLine($category, 'H');
            case "week":
                return $this->labelsForLine($category, 'l');
            case "month":
                return $this->labelsForLine($category);
            case "year":
                return $this->labelsForLine($category, 'F Y');
            case "custom":
                return $this->labelsForLine($category);
        }
    }

    /**
     * 
     * Get the date range
     * 
     * @param \Carbon\Carbon $start_date
     * @param string $category
     * @param string $format
     * @return array carbon object's array
     */
    public function generateDateRange(\Carbon\Carbon $start_date, $category, $format = "Y-m-d") {
        $end_date = \Carbon\Carbon::now()->tz(timezone());
        $dates = [];
        if ($category == "week" || $category == "month") {
            for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
                $dates[] = $date->format($format);
            }
        }
        if ($category == "today") {
            for ($date = $start_date; $date->lte($end_date); $date->addHour()) {
                $dates[] = $date->format($format);
            }
        }
        if ($category == "year") {
            for ($date = $start_date; $date->lte($end_date); $date->addMonth()) {
                $dates[] = $date->format($format);
            }
        }
        if ($category == "custom") {
            $start_date = $this->getCarbon($this->request->input('start'));
            $end_date = $this->getCarbon($this->request->input('end'), false);
            for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
                $dates[] = $date->format($format);
            }
        }

        return $dates;
    }

    /**
     * 
     * get date range
     * 
     * @param string $category
     * @return array
     */
    public function dateRange($category = "month") {
        $end_date = \Carbon\Carbon::now();
        switch ($category) {
            case "today":
                $start_date = \Carbon\Carbon::now()->subDay();
                return [$start_date, $end_date];
            case "month":
                $start_date = \Carbon\Carbon::now()->subMonth();
                return [$start_date, $end_date];
            case "week":
                $start_date = \Carbon\Carbon::now()->subDays(7);
                return [$start_date, $end_date];
            case "year":
                $start_date = \Carbon\Carbon::now()->subYear();
                return [$start_date, $end_date];
            case "custom":
                $start_date = $this->getCarbon($this->request->input('start'));
                $end_date = $this->getCarbon($this->request->input('end'), false);
                return [$start_date, $end_date];
        }
    }

    /**
     * 
     * select the chart type
     * 
     * @param object $tickets
     * @param string $chart
     * @param string $category
     * @return json
     */
    public function selectChart($tickets, $chart = "doughnut", $category = "month") {
        switch ($chart) {
            case "doughnut":
                return $this->doughnut($tickets, $category);
            case "line":
                return $this->line($tickets, $category);
        }
    }

    /**
     * 
     * get the all tickets
     * 
     * @return json
     */
    public function all() {
        $chart = $this->request->input('chart','line');
        $category = $this->request->input('category','today');
        $join = $this->tickets();
        $tickets = $join
                ->select(
                        DB::raw('COUNT(tickets.id) as tickets'), 'ticket_status.name as name', DB::raw("DATE_FORMAT(tickets.created_at, '%Y-%m-%d') as date")
                )
                ->when($category, function($query) use ($category) {
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
     * grouping the tickets
     * 
     * @param object $tickets
     * @param string $category
     * @return collection
     */
    public function group($tickets, $category = "month") {
        $statuses_array = \App\Model\helpdesk\Ticket\Ticket_Status::pluck('name')->flip();
        $status = [];
        foreach ($statuses_array as $key => $arr) {
            $status[$key] = 0;
        }
        $statuses_collection = collect($status);
        $ticket_collection = collect($tickets->toArray());
        //dd($ticket_collection);
        $ticket_by_dates = $ticket_collection->groupBy('date');
        $dates_array = $this->labelsForLine($category);
        $dates_collection = collect($dates_array);

        $flip = $dates_collection->flip();

        $merge = $flip->merge($ticket_by_dates)->toArray();

        $tickets = [];
        foreach ($merge as $key => $ticket) {
            if (!is_array($ticket)) {
                $tickets[$key] = $statuses_collection->toArray();
            } else {
                foreach ($ticket as $k => $v) {
                    $tickets[$key][$v['name']] = $v['tickets'];
                }
                $tickets[$key] = $statuses_collection->merge($tickets[$key])->toArray();
            }
        }
        $col = collect($tickets);
        return $col;
    }

    /**
     * 
     * filte rthe ticket according status
     * 
     * @param object $tickets
     * @param string $name
     * @return array
     */
    public function filter($tickets, $name = "Open") {
        $status = [];
        foreach ($tickets as $date => $ticket) {
            $status[] = (int) $ticket[$name];
        }
        return $status;
    }

    /**
     * 
     * return the report view
     * 
     * @return view
     */
    public function allView() {
        if(!User::has('report')){
            return redirect('/')->with('fails', \Lang::get('lang.access-denied'));
        }
        return view('report::all');
    }

    /**
     * 
     * get ticket according  priority
     * 
     * @return json
     */
    public function priority() {
        $chart = $this->request->input('chart');
        $category = $this->request->input('category');
        $join = $this->tickets();
        $tickets = $join
                ->when($category, function($query) use ($category) {
                    $ranges = $this->dateRange($category);
                    return $query->whereBetween('tickets.created_at', $ranges);
                })
                ->select(DB::raw('COUNT(tickets.id) as tickets'), 'ticket_priority.priority as name')
                ->groupBy('ticket_priority.priority')
                ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     * 
     * get ticket according Sla
     * 
     * @return json
     */
    public function sla() {
        $chart = $this->request->input('chart');
        $category = $this->request->input('category');
        $join = $this->tickets();
        $tickets = $join
                ->when($category, function($query) use ($category) {
                    $ranges = $this->dateRange($category);
                    return $query->whereBetween('tickets.created_at', $ranges);
                })
                ->select(DB::raw('COUNT(tickets.id) as tickets'), 'sla_plan.name as name')
                ->groupBy('sla_plan.name')
                ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     * 
     * get ticket according helptopic
     * 
     * @return json
     */
    public function helptopic() {
        $chart = $this->request->input('chart');
        $category = $this->request->input('category');
        $join = $this->tickets();
        $tickets = $join
                ->when($category, function($query) use ($category) {
                    $ranges = $this->dateRange($category);
                    return $query->whereBetween('tickets.created_at', $ranges);
                })
                ->select(DB::raw('COUNT(tickets.id) as tickets'), 'help_topic.topic as name')
                ->groupBy('help_topic.topic')
                ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     * 
     * get ticket according status
     * 
     * @return json
     */
    public function status() {
        $chart = $this->request->input('chart');
        $category = $this->request->input('category');
        $join = $this->tickets();
        $tickets = $join
                ->when($category, function($query) use ($category) {
                    $ranges = $this->dateRange($category);
                    return $query->whereBetween('tickets.created_at', $ranges);
                })
                ->select(DB::raw('COUNT(tickets.id) as tickets'), 'ticket_status.name as name')
                ->groupBy('ticket_status.name')
                ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     * 
     * get ticket according status type
     * 
     * @return json
     */
    public function statusType() {
        $chart = $this->request->input('chart');
        $category = $this->request->input('category');
        $join = $this->tickets();
        $tickets = $join
                ->when($category, function($query) use ($category) {
                    $ranges = $this->dateRange($category);
                    return $query->whereBetween('tickets.created_at', $ranges);
                })
                ->select(DB::raw('COUNT(tickets.id) as tickets'), 'ticket_status_type.name as name')
                ->groupBy('ticket_status_type.name')
                ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     * 
     * get ticket according source
     * 
     * @return json
     */
    public function source() {
        $chart = $this->request->input('chart');
        $category = $this->request->input('category');
        $join = $this->tickets();
        $tickets = $join
                ->when($category, function($query) use ($category) {
                    $ranges = $this->dateRange($category);
                    return $query->whereBetween('tickets.created_at', $ranges);
                })
                ->select(DB::raw('COUNT(tickets.id) as tickets'), 'ticket_source.name as name')
                ->groupBy('ticket_source.name')
                ->get();
        return $this->selectChart($tickets, $chart, $category);
    }

    /**
     * 
     * get carbon time 
     * 
     * @return object
     */
    public function getCarbon($date, $glue = '-', $format = "Y-m-d", $flag = true) {
        $parse = explode($glue, $date);
        //dd($format);
        if ($format == "Y-m-d") {
            $day = $parse[2];
            $month = $parse[1];
            $year = $parse[0];
        }
        if ($format == "m-d-Y") {
            $month = $parse[0];
            $day = $parse[1];
            $year = $parse[2];
        }

        $hour = 0;
        $minute = 0;
        $second = 0;
        if (!$flag) {
            $hour = 23;
            $minute = 59;
            $second = 59;
        }
        $carbon = \Carbon\Carbon::create($year, $month, $day, $hour, $minute, $second);
        return $carbon;
    }

    /**
     * export the ticket details
     */
    public function export() {
        try {
            $this->deleteExportDirectory();
            $request = $this->request->input('fields');
            $date = $this->request->input("date");
            $dates = explode(" - ", $date);
            $start = carbon($dates[0]);
            $end = carbon($dates[1]);
            $ticket = $this->tickets()
                    ->leftJoin('ticket_thread', function($join) {
                        $join->on('tickets.id', '=', 'ticket_thread.ticket_id')
                        ->whereNotNull('ticket_thread.title')->where('ticket_thread.title', '!=', "");
                    })
                    ->whereDate('tickets.created_at', ">", $start)
                    ->whereDate('tickets.created_at', "<", $end);
            $tickets = $ticket->select($request)->get()->toArray();
            if (count($tickets) > 0) {
                $filename = "Tickets $start-$end";
                $excel = \Excel::create($filename, function($excel) use($tickets) {
                            $excel->sheet('sheet', function($sheet) use($tickets) {
                                $sheet->fromArray($tickets);
                            });
                        });
                $excel->store("csv");
            }
            return response('success', 200);
        } catch (\Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }
    
    public function deleteExportDirectory(){
        $dir = storage_path('export');
        if(is_dir($dir)){
            rmdir($dir);
        }
    }

    public function download() {
        $files = scandir(storage_path('exports'), 1);
        $file = storage_path('exports') . '/' . checkArray(0, $files);
        if (\File::exists($file)) {
            return response()->download($file);
        }
    }

    public function allExport() {
        $date = $this->request->input("interval");
        $dates = explode(" - ", $date);
        $start = $this->getCarbon($dates[0], "/", "m-d-Y");
        $end = $this->getCarbon($dates[1], "/", "m-d-Y", false);
        $ticket = $this->tickets()
                ->leftJoin('ticket_thread', function($join) {
                    $join->on('tickets.id', '=', 'ticket_thread.ticket_id')
                    ->whereNotNull('ticket_thread.title');
                })
                ->whereBetween('tickets.created_at', [$start, $end]);
        $tickets = $ticket->select(
                'tickets.ticket_number', 
                'ticket_thread.title', 
                'ticket_source.name as source', 
                'department.name as department', 
                'tickets.duedate', 
                'tickets.created_at as created', 
                'client.user_name as client_user_name', 
                'client.email as client_email', 
                'client.first_name as client_first_name', 
                'client.last_name as as client_last_name', 
                'agent.user_name as agent_user_name', 
                'agent.email as agent_email', 
                'agent.first_name as agent_first_name', 
                'agent.last_name as agent_last_name',
                'ticket_priority.priority'
                )->get()->toArray();
        if (count($tickets) > 0) {
            $filename = "Tickets " . $start->format('m-d-y') . "-" . $end->format('m-d-y');
            $excel = \Excel::create($filename, function($excel) use($tickets) {
                        $excel->sheet('sheet', function($sheet) use($tickets) {
                            $sheet->fromArray($tickets);
                        });
                    });
            return $excel->store('csv', false, true);
        }
    }

    /**
     * 
     * get the report icon on admin panel settings page
     * 
     * @return string
     */
    public function icon() {
        return '<div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="' . url('report/get') . '">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-pie-chart fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >' . Lang::get('lang.report') . '</p>
                    </div>
                </div>';
    }

    /**
     * 
     * get the view of different aspects like department, team, agents etc..
     * 
     * @return view
     */
    public function groupView() {
        if(!User::has('report')) {
            return redirect()->to('/')->with('fails', \Lang::get('lang.access-denied'));
        }
        return view("report::group");
    }

    /**
     * get the user's email according the request from front end
     * @return array
     */
    public function MailTo() {
        $term = $this->request->input('term');
        $users = \App\User::where('email', 'LIKE', '%' . $term . '%')->pluck('email')->toArray();
        return $users;
    }

    /**
     * 
     * message on report mail
     * 
     * @param string $context
     * @return string
     */
    public function reportMessage($context) {
        $text = "";
        if ($context == 'agents') {
            $text = "Hi,<br><br>Please see the report of agents. <br><br>Thanks.";
            if ($this->request->input('message')) {
                $text = $this->request->input('message');
            }
        }
        if ($context == 'teams') {
            $text = "Hi,<br><br>Please see the report of groups. <br><br>Thanks.";
            if ($this->request->input('message')) {
                $text = $this->request->input('message');
            }
        }
        if ($context == 'departments') {
            $text = "Hi,<br><br>Please see the report of departments. <br><br>Thanks.";
            if ($this->request->input('message')) {
                $text = $this->request->input('message');
            }
        }
        if ($context == 'all') {
            $text = "Hi,<br><br>Please see the overall report. <br><br>Thanks.";
            if ($this->request->input('message')) {
                $text = $this->request->input('message');
            }
        }
        
        if ($context == 'timesheet') {
            $text = "Hi,<br><br>Please see the timesheet report. <br><br>Thanks.";
            if ($this->request->input('message')) {
                $text = $this->request->input('message');
            }
        }

        return $text;
    }

    /**
     * 
     * Get subject on report mail
     * 
     * @param string $context
     * @return string
     */
    public function reportSubject($context) {
        $text = "";
        if ($context == 'agents') {
            $text = "Agent Report";
            if ($this->request->input('subject')) {
                $text = $this->request->input('subject');
            }
        }
        if ($context == 'teams') {
            $text = "Team Report";
            if ($this->request->input('subject')) {
                $text = $this->request->input('subject');
            }
        }
        if ($context == 'departments') {
            $text = "Department Report";
            if ($this->request->input('subject')) {
                $text = $this->request->input('subject');
            }
        }
        if ($context == 'all') {
            $text = "Overall Report";
            if ($this->request->input('subject')) {
                $text = $this->request->input('subject');
            }
        }
        
        if ($context == 'timesheet') {
            $text = "Timesheet Report";
            if ($this->request->input('subject')) {
                $text = $this->request->input('subject');
            }
        }

        return $text;
    }

    /**
     * 
     * mailing from report 
     * 
     * @param string $path
     * @param string $context
     */
    public function mailing($path, $context) {
        $subject = $this->reportSubject($context);
        $from = $this->request->input('from');
        $to = $this->request->input('send_agents');
        $body = $this->reportMessage($context);
        $phpmail = new \App\Http\Controllers\Common\PhpMailController();
        $attachments = [];
        if($path=='No data to export'){
            throw new Exception($path, 500);
        }
        if (File::exists($path['full'])) {
            $attachments = [[
            'file_path' => $path['full'],
            'file_name' => $path['file'],
            'mime' => File::type($path['full']),
                ],
            ];
        }
        $message = ['subject' => $subject, 'body' => $body, 'attachments' => $attachments];
        $users = \App\User::whereIn('id',$to)->select('email')->get();
        foreach ($users as $user) {
            if($user->email){
                $phpmail->sendmail($from, ['email' => $user->email], $message, []);
            }
        }
        if (File::exists($path['full'])) {
            File::delete($path['full']);
        }
    }

    /**
     * 
     * post mail for report all
     * 
     * @return json
     */
    public function mail() {
        $this->validate($this->request, [
            'to' => 'required',
            'subject' => 'max:20',
        ]);

        try {
            $path = $this->allExport(true);
            $this->mailing($path, 'all');
            $message = "Mail has sent";
            $status_code = 200;
        } catch (Exception $ex) {
            $message = [$ex->getMessage()];
            $status_code = 500;
        }
        return $this->mailResponse($message, $status_code);
    }

    /**
     * 
     * get response json response
     * 
     * @param string $message
     * @param int $code
     * @return json
     */
    public function mailResponse($message, $code = 200) {
        return response()->json([$message], $code);
    }

}
