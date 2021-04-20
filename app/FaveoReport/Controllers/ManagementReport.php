<?php
namespace App\FaveoReport\Controllers;

use App\FaveoReport\Jobs\ManagementReportExportJob;
use App\FaveoReport\Models\ReportDownload;
use App\Http\Controllers\Controller;
use App\Model\Custom\Required;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Agent_panel\User_org;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Ticket\Tickets;
use App\User;
use Carbon\Carbon;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Lang;
use Zipper;

/**
 * ManagementReport report
 *
 * @abstract ReportController
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 */
class ManagementReport extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * It returns Management performance view
     */
    public function getView()
    {
        if (!User::has('report')) {
            return redirect('/')->with('fails', Lang::get('lang.access-denied'));
        }

        $maxDateRange = (new CommonSettings)->getOptionValue('reports_max_date_range')->first()->option_value;

        return view("report::Management.management", compact('maxDateRange'));
    }

    /**
     * Required tickets informations are getting through the relations
     */
    public function ManagementQuery(Request $request)
    {
        $agents        = $request->input('agents');
        $departments   = $request->input('departments');
        $client        = $request->input('clients');
        $source        = $request->input('sources');
        $priority      = $request->input('priorities');
        $type          = $request->input('types');
        $status        = $request->input('status');
        $helptopic     = $request->input('helptopic');
        $team          = $request->input('team');
        $createStart   = $request->input('start_date');
        $createEnd     = $request->input('end_date');
        $updateStart   = $request->input('update_start');
        $updateEnd     = $request->input('update_end');
        $agentTimezone = agentTimezone();
        $dateFormat    = dateTimeFormat();
        $creator_ids      = $request->input('creator');

        if (empty($createStart) && empty($updateStart)) {
            $createStart = Carbon::now()->subMonth()->addDay()->format('Y-m-d');
            $createEnd   = Carbon::now()->format('Y-m-d');
        }

        $tickets = Tickets::whereNotIn('tickets.status', getStatusArray('merged'))->when($createStart, function ($q) use ($createStart, $createEnd, $agentTimezone) {
            $q->whereBetween('tickets.created_at', [
                Carbon::parse($createStart, $agentTimezone)->timezone('UTC'),
                Carbon::parse($createEnd, $agentTimezone)->endOfDay()->timezone('UTC'),
            ]);
        })
            ->when($updateStart, function ($q) use ($updateStart, $updateEnd, $agentTimezone) {
                $q->whereBetween('tickets.updated_at', [
                    Carbon::parse($updateStart, $agentTimezone)->timezone('UTC'),
                    Carbon::parse($updateEnd, $agentTimezone)->endOfDay()->timezone('UTC'),
                ]);
            })
            ->when($agents, function ($q) use ($agents) {
                $q->whereIn('assigned_to', $agents);
            })
            ->when($departments, function ($q) use ($departments) {
                $q->whereIn('dept_id', $departments);
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
            ->when($status, function ($q) use ($status) {
                $q->whereIn('status', $status);
            })
            ->when($helptopic, function ($q) use ($helptopic) {
                $q->whereIn('help_topic_id', $helptopic);
            })
            ->when($team, function ($q) use ($team) {
                $q->whereIn('team_id', $team);
            })
            ->when($creator_ids, function ($q) use ($creator_ids) {
                $q->whereIn('creator_id', $creator_ids);
            })
            ->with([
                'assigned:id,email,first_name,last_name',
                'assignedTeam:id,name',
                'statuses:id,name',
                'types:id,name',
                'priority:priority_id,priority',
                'user:id,email',
                'departments:id,name',
                'helptopic:id,topic',
                'sources:id,name',
                'customFieldValues',
                'creator:id,user_name,first_name,last_name,profile_pic,email'
            ])
            ->latest()
            ->select();

        // fetch total ticket count
        $total_tickets = $tickets->count();

        // add limit to query
        $tickets->limit((int) $request->length);

        // add offset to query
        $tickets->offset($request->start);

        return \DataTables::of($tickets)
            ->addColumn('ticket_number', function ($tickets) {
                return ($tickets->ticket_number);
            })
            ->addColumn('subject', function ($tickets) {
                return utfEncoding($tickets->thread()->orderBy('id')->pluck('title')->first());
            })
            ->addColumn('statuses', function ($tickets) {
                return ($tickets->statuses->name);
            })
            ->addColumn('created_at', function ($tickets) use ($agentTimezone, $dateFormat) {
                return $tickets->created_at->timezone($agentTimezone)->format($dateFormat);
            })
            ->addColumn('types', function ($tickets) {
                return ($tickets->types['name']);
            })
            ->addColumn('priority', function ($tickets) {
                return ($tickets->priority->priority);
            })
            ->addColumn('org_name', function ($tickets) {
                $orgId = User_org::where('user_id', $tickets->user_id)->pluck('org_id')->toArray();
                if ($orgId) {
                    $organizationName = Organization::wherein('id', $orgId)->pluck('name')->toArray();
                    return (implode(",", $organizationName));
                }
            })
            ->addColumn('email', function ($tickets) {
                return is_null($tickets->user) ? null : $tickets->user->email;
            })
            ->addColumn('location', function ($tickets) {
                return ($tickets->location['title']);
            })
            ->addColumn('departments', function ($tickets) {
                return ($tickets->departments->name);
            })
            ->addColumn('helptopic', function ($tickets) {
                return ($tickets->helptopic->topic);
            })
            ->addColumn('sources', function ($tickets) {
                return ($tickets->sources->name);
            })
            ->addColumn('description', function ($tickets) {
                $desc = $tickets->thread->where('title', '!=', '')->first();
                if ($desc) {
                    return $this->escapeContent($desc->body);
                }
            })
            ->addColumn('reply_content', function ($tickets) {
                $reply = $tickets->thread->where('thread_type', 'first_reply')->where('poster', 'support')->where('title', '')->first();
                if ($reply) {
                    return $this->escapeContent($reply->body);
                }
            })
            ->addColumn('agent_responded_time', function ($tickets) use ($agentTimezone, $dateFormat) {
                $reply_date_time = $tickets->thread->where('thread_type', 'first_reply')->where('poster', 'support')->where('title', '')->first();
                if ($reply_date_time) {
                    return $reply_date_time->created_at->timezone($agentTimezone)->format($dateFormat);
                }
            })
            ->addColumn('last_response', function ($tickets) use ($agentTimezone, $dateFormat) {
                return $tickets->updated_at->timezone($agentTimezone)->format($dateFormat);
            })
            ->addColumn('closed_at', function ($tickets) use ($agentTimezone, $dateFormat) {
                if ($tickets->statuses->id == 3 || $tickets->statuses->id == 2) {
                    if ($tickets->closed_at) {
                        return $tickets->closed_at->timezone($agentTimezone)->format($dateFormat);
                    }
                }
            })
            ->addColumn('assigned', function ($tickets) {
                return $tickets->assigned ? $tickets->assigned->full_name : null;
            })
            ->addColumn('assigned_team', function ($tickets) {
                if ($tickets->assignedTeam) {
                    return $tickets->assignedTeam->name;
                }
            })
            ->addColumn('is_response_sla', function ($tickets) {
                if ($tickets->is_response_sla == 1) {
                    return Lang::get("report::lang.yes");
                }
                return Lang::get("report::lang.no");
            })
            ->addColumn('is_resolution_sla', function ($tickets) {
                if ($tickets->statuses->name == 'Closed' || $tickets->statuses->name == 'Resolved') {

                    if ($tickets->is_resolution_sla == 1) {
                        return Lang::get("report::lang.yes");
                    }
                }
                return Lang::get("report::lang.no");
            })
            ->addColumn('duedate', function ($tickets) use ($agentTimezone, $dateFormat) {
                $duedate = $tickets->duedate;

                return is_null($duedate) ? null : $duedate->timezone($agentTimezone)->format($dateFormat);
            })
            ->addColumn('overdue', function ($tickets) {
                if ($tickets->duedate != null) {
                    $now            = strtotime(Carbon::now()->timezone('UTC'));
                    $duedate        = strtotime($tickets->duedate);
                    $check_due_time = $now;
                    $due_status     = $duedate - $check_due_time;
                    $check_due_time = $now;
                    return ($due_status < 0) ? Lang::get("report::lang.yes") : Lang::get("report::lang.no");
                }
                if ($tickets->statuses->name == 'Closed') {
                    return ($tickets->is_resolution_sla == 1) ? Lang::get("report::lang.no") : Lang::get("report::lang.yes");
                }
                return Lang::get('lang.sla-halted');

            })
            ->addColumn('first_resp_time_bh', function ($tickets) {
                $createdAt = $tickets->created_at;
                $replyTime = $tickets->thread->where('thread_type', 'first_reply')->where('poster', 'support')->where('title', '')->first();

                return is_null($replyTime) ? '--' : $replyTime->created_at->diffForHumans($createdAt, true, false, 6);
            })
            
             ->addColumn('creator', function ($tickets) {
                 $creator = User::where('id',$tickets->creator_id)->select('first_name','last_name')->first();
                return $creator['first_name'].' '.$creator['last_name'];
            })
            ->addColumn('time_tracked', function($tickets){
                return $tickets->totalTimeTracked();
            })
            ->rawColumns(['ticket_number', 'statuses', 'created_at', 'types', 'priority', 'email', 'departments', 'helptopic', 'sources', 'description', 'reply_content', 'last_response', 'closed_at', 'assigned', 'is_response_sla', 'is_resolution_sla', 'subject', 'agent_responded_time', 'duedate', 'overdue', 'first_resp_time_bh','creator'])
            ->setTotalRecords($total_tickets)
            ->make(true);
    }

    /**
     * Trigger management report export job
     *
     * @param Request instance
     * @return Json http response
     */
    public function triggerManagementReportExport(Request $request)
    {
        $active_queue = (new \App\Model\MailJob\QueueService())->where('status', 1)->first();

        if ($active_queue) {
            $short = $active_queue->short_name;

            // If sync return error
            if ($short == 'sync') {
                $response['status']  = 'failed';
                $response['message'] = Lang::get('report::lang.report_export_not_supported_with_sync');

                return response()->json($response);
            }

            app('queue')->setDefaultDriver($short);
        }

        // Create fresh report export
        $report = $this->createReportExport();

        try {
            // Dispatch export job
            ManagementReportExportJob::dispatch($request->all(), $report)->onQueue('reports');

            // Set response
            $response['status']  = 'success';
            $response['message'] = Lang::get('report::lang.report_export_successful');
        } catch (Exception $e) {
            // Remove recently created report export
            $report->delete();

            $response['status']  = 'failed';
            $response['message'] = Lang::get('report::lang.report_export_failed');
        }

        return response()->json($response);
    }

    /**
     * Escape message body with unwanted html
     *
     * @param String $content Unescaped string
     * @return String Escaped string
     */
    protected function escapeContent($content)
    {
        // Remove inline styles
        $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
        $content = preg_replace('/@media[^{]+\{([\s\S]+?\})\s*\}/i', '', $content);

        // Strip html tags except table tag
        $content = strip_tags($content, "<table><style>");

        // Strip other tags except table tag
        return $this->stripTagsContent($content, '<table>');
    }

    /**
     * Escape html from string
     *
     * @param String Unescapad content
     * @param Strint Html tags to ignore
     * @param Bool $invert Add given html tags
     * @return String Escaped string
     */
    private function stripTagsContent($text, $tags = '', $invert = false)
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) and count($tags) > 0) {
            if ($invert == false) {
                return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif ($invert == false) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        return $text;
    }

    /**
     * Create fresh report export
     *
     * @return ReportDownload instance
     */
    private function createReportExport()
    {
        return auth()->user()->reports()->create([
            'file'       => 'management_report-' . faveoDate(null, 'dmYhmi'),
            'ext'        => 'xls',
            'type'       => 'Management Report',
            'hash'       => Str::random(60),
            'expired_at' => Carbon::now()->addHours(6),
        ]);
    }
}
