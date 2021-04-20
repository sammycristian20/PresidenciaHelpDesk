<?php

namespace App\Plugins\Migration\Controllers;

use App\Http\Controllers\Controller;
use Input;
use Hash;

class SpiceworkMigration extends Controller
{

    public $users = [];
    public $count = 0;

    public function import()
    {
        $exporting_mode = Input::get('mode', 'user');
        ini_set('memory_limit', '-1');
        $path           = storage_path('exported_data.json');
        $array          = json_decode(file_get_contents($path), true);
        $this->users    = collect($array['users']);
        if ($exporting_mode == 'user') {
            $users = $array['users'];
            return $this->usersParsing($users);
        }
        if ($exporting_mode == 'tickets') {
            \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            $tickets = $array['tickets'];
            return $this->ticketParsing($tickets);
        }
    }
    public function ticketParsing($tickets = [])
    {
        ini_set('memory_limit','-1');
        set_time_limit(0);
        $skip               = Input::get('skip', 0);
        $take               = Input::get('take', 10);
        $collection_tickets = collect($tickets)->splice($skip)->take($take);
        $this->exportTicket($collection_tickets);
        $next_skip          = $skip + $take;
        if ($collection_tickets->count() > 0) {
            $response = ['completed' => $next_skip . " Tickets", 'url' => url('migration/migrate?mode=tickets&skip=' . $next_skip . '&take=' . $take . '&app=spicework')];
        }
        else {
            $response = ['message' => 'success', 'url' => null];
        }
        \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        return response()->json(compact('response'));
    }
    public function usersParsing($users = [])
    {
        $skip = Input::get('skip', 0);
        $take = Input::get('take', 100);

        $collection_users = collect($users)->splice($skip)->take($take);
        $this->exportUsers($collection_users);
        $next_skip        = $skip + $take;
        if ($collection_users->count() > 0) {
            $response = ['completed' => $next_skip . " Users", 'url' => url('migration/migrate?mode=user&skip=' . $next_skip . '&take=' . $take . '&app=spicework')];
        }
        else {
            $response = ['message' => 'Users migrated ', 'url' => url('migration/migrate?mode=tickets&app=spicework')];
        }
        return response()->json(compact('response'));
    }
    public function exportTicket($tickets = [], $des = true)
    {
        foreach ($tickets as $ticket) {
            $assigned_to_import_id = checkArray('assigned_to', $ticket);
            $requester_import_id   = checkArray('created_by', $ticket);
            $assigned_to           = $this->getUserFromFaveo($assigned_to_import_id);
            $requester_id          = $this->getUserFromFaveo($requester_import_id);
            $created_at            = checkArray('created_at', $ticket);
            $closed_at             = checkArray('closed_at', $ticket);
            $subject               = checkArray('summary', $ticket);
            $status                = checkArray('status', $ticket);
            $ticket_import_id      = checkArray('import_id', $ticket);
            $threads = checkArray('Comments', $ticket);
            $description           = [
                'created_by' => $requester_import_id,
                'body'       => checkArray('description', $ticket),
                'is_public'  => 0,
                'created_at' => $created_at
            ];

            if (!$des) {
                
                if (is_array($threads)) {
                    $threads = array_push($threads, $description);
                    //$threads = array_merge($threads, $description);
                }
                else {
                    $threads = [$description];
                }
            }
            else {
                if (is_array($threads)) {
                    $threads = array_merge($threads, [$description]);;
                    //$threads = array_merge($threads, $description);
                }
                 //$threads = [$description];
                 // dd($threads);
            }

            $ticket = [
                'user_id'          => $requester_id,
                'assigned_to'      => $assigned_to,
                'status'           => $status,
                'ticket_import_id' => $ticket_import_id,
                'closed_at'        => carbon($closed_at),
                'created_at'       => carbon($created_at),
                'subject'          => $subject,
            ];

            $this->createTicket($ticket, $threads);
        }
    }
    public function getUserFromFaveo($import_id, $model = false)
    {
        $assigned = NULL;
        if ($import_id) {
            $user      = $this->users->where('import_id', $import_id)->first();
            $requester = $this->importUser($user);
            if ($model) {
                return $requester;
            }
            if ($requester) {
                $assigned = $requester->id;
            }
        }
        return $assigned;
    }
    public function exportUsers($users = [])
    {
        foreach ($users as $user) {
            if ($user) {
                $this->importUser($user);
            }
        }
    }
    public function importUser($user = [])
    {
        $mobile    = null;
        $phone     = "";
        $last_name = "";
        $role      = checkArray('role', $user);
        if (checkArray('first_name', $user)) {
            $first_name = checkArray('first_name', $user);
        }
        else {
            $first_name = checkArray('email', $user);
        }
        if (checkArray('cell_phone', $user)) {
            $mobile = checkArray('cell_phone', $user);
        }
        if (checkArray('office_phone', $user)) {
            $phone = checkArray('office_phone', $user);
        }
        $result['email']      = checkArray('email', $user);
        $result['first_name'] = $first_name;
        $result['last_name']  = $last_name;
        $result['role']       = $this->role($role);
        $result['mobile']     = $mobile;
        $result['phone']      = $phone;
        return $this->createRequester($result);
    }
    public function role($role)
    {
        switch ($role) {
            case "admin":
                return 'admin';
            case "helpdesk_admin":
                return 'admin';
            case "end_user":
                return "user";
            default :
                return "admin";
        }
    }
    public function createRequester($raw)
    {
        if ($raw && $raw['email']) {
            $str       = str_random(8);
            $requester = \App\User::updateOrCreate(
                            ['user_name' => $raw['email'],], [

                        'first_name'   => $raw['first_name'],
                        'last_name'    => $raw['last_name'],
                        'email'        => $raw['email'],
                        'role'         => $raw['role'],
                        'active'       => '1',
                        'mobile'       => $raw['mobile'],
                        'phone_number' => $raw['phone'],
                        'password'     => Hash::make($str)
            ]);
            return $requester;
        }
    }
    public function createTicket($tickets_array = [], $thread = [])
    {
        //dd($testModal);
        $prefix          = "SPW-";
        if(array_key_exists("user_id", $tickets_array))
            $user_id     = checkArray('user_id', $tickets_array);
        else
            $user_id     = checkArray('import_id', $tickets_array);
        $assigned_to     = checkArray('assigned_to', $tickets_array);
        $status          = checkArray('status', $tickets_array);
        $created_at      = checkArray('created_at', $tickets_array);
        $closed_at       = checkArray('closed_at', $tickets_array);
        $subject         = checkArray('subject', $tickets_array);
        $dept_id         = 1;
        $tickets         = new \App\Model\helpdesk\Ticket\Tickets();
        $tickets->notify = false;
        $ticket_number   = $prefix . checkArray('ticket_import_id', $tickets_array);
        $first_ticket    = \App\Model\helpdesk\Ticket\Tickets::where('ticket_number', $ticket_number)->select('id')->with('thread')->first();
        if (!$first_ticket) {
            $ticket = $tickets->updateOrCreate(
                  [
                'ticket_number' => $ticket_number,
                'duedate'       => NULL,
                'user_id'       => $user_id,
                'assigned_to'   => $assigned_to,
                'sla'           => $this->sla(),
                'priority_id'   => $this->priority(),
                'status'        => $this->status($status),
                'type'          => $this->type(),
                'source'        => $this->source(),
                'dept_id'       => $dept_id,
                'help_topic_id' => $this->helpTopic($dept_id),
                'created_at'    => $created_at,
                'closed_at'     => $closed_at,
                    ]
            );
            $this->thread($ticket, $thread, $subject);
            return $ticket;
        }
        else{
            $data = \App\Model\helpdesk\Ticket\Tickets::where('id', $first_ticket->id)
                        ->update([
                            'ticket_number' => $ticket_number,
                            'duedate'       => NULL,
                            'user_id'       => $user_id,
                            'assigned_to'   => $assigned_to,
                            'sla'           => $this->sla(),
                            'priority_id'   => $this->priority(),
                            'status'        => $this->status($status),
                            'type'          => $this->type(),
                            'source'        => $this->source(),
                            'dept_id'       => $dept_id,
                            'help_topic_id' => $this->helpTopic($dept_id),
                            'created_at'    => $created_at,
                            'closed_at'     => $closed_at,
                        ]);
            $ticket = $this->updateThread($first_ticket, $thread, $subject);
            return $ticket;
        }

    }
    public function status($type)
    {
        $status_id = NULL;
        $statuses  = new \App\Model\helpdesk\Ticket\Ticket_Status();
        $status    = $statuses->whereHas('type', function($q) use($type) {
                    $q->where('name', $type);
                })
                ->where('default', 1)
                ->first();
        if (!$status) {
            $status = $statuses->select('id')->first();
        }
        if ($status) {
            $status_id = $status->id;
        }
        return $status_id;
    }
    public function helpTopic($dept_id)
    {
        if ($dept_id) {
            $help = \App\Model\helpdesk\Manage\Help_topic::where('department', $dept_id)->where('status', '1')->first();
        }
        if (!$help) {
            $help = \App\Model\helpdesk\Manage\Help_topic::where('status', '1')->first();
        }
        return $help->id;
    }
    public function slaPlan()
    {
        $sla = \App\Model\helpdesk\Manage\Sla\Sla_plan::where('status', 1)
                ->with(['target' => function($query) {
                        return $query->select('sla_id', 'id', 'priority_id');
                    }])
                ->select('id', 'name', 'sla_target')
                ->first();
        return $sla;
    }
    public function sla()
    {
        $sla_id = "";
        $sla    = $this->slaPlan();
        if ($sla) {
            $sla_id = $sla->id;
        }
        return $sla_id;
    }
    public function priority()
    {
        $priority_id = NULL;
        $target      = $this->slaPlan()->target;
        if ($target) {
            $priority_id = $target->priority_id;
        }
        return $priority_id;
    }
    public function source()
    {
        $source_id = NULL;
        $source    = \App\Model\helpdesk\Ticket\Ticket_source::first();
        if ($source) {
            $source_id = $source->id;
        }
        return $source_id;
    }
    public function type()
    {
        $type_id = NULL;
        $type    = \App\Model\helpdesk\Manage\Tickettype::where('is_default', 1)->first();
        if ($type) {
            $type_id = $type->id;
        }
        return $type_id;
    }
    public function thread($ticket, $threads = [], $subject = "")
    {
        foreach ($threads as $key => $thread) {

            $requester_import_id = checkArray('created_by', $thread);
            $requester           = $this->getUserFromFaveo($requester_import_id, true);
            $poster              = 'client';
            $requester_id        = null;
            if ($requester && $requester->role != 'user') {
                $poster = 'support';
            }
            if ($key !== 0) {
                $subject = "";
            }
            if ($requester) {
                $requester_id = $requester->id;
            }

            $ticket->thread()->create([
                'user_id'     => $requester_id,
                'poster'      => $poster,
                //'body'        => str_replace("\n", "<br>",checkArray('body', $thread)),
                'body'        => checkArray('body', $thread),
                'title'       => $subject,
                'is_internal' => $this->internal($thread),
                'created_at'  => carbon(checkArray('created_at', $thread))
            ]);
            $log = ['user_id'     => $requester_id,
                'poster'      => $poster,
                //'body'        => str_replace("\n", "<br>",checkArray('body', $thread)),
                'body'        => checkArray('body', $thread),
                'title'       => $subject,
                'is_internal' => $this->internal($thread),
                'created_at'  => carbon(checkArray('created_at', $thread))];
                
                loging("SpiceworkMigration", "New thread Created  : ".json_encode($log), "info");
           // \Log::info("New thread Created:-     ".$log."    ---------------end------------");
        }
    }

    public function updateThread($ticket, $threads = [], $subject = ""){
        foreach ($threads as $key => $thread) {
            $this->thread_exists($thread, $ticket, $subject);
        }
        return $ticket;
    }

    public function thread_exists($data, $ticket, $subject = ""){
        //dd($data);
        $thread = new \App\Model\helpdesk\Ticket\Ticket_Thread();
        $import_user = $this->users->where('import_id', $data['created_by'])->first();
        $user = \App\User::where('user_name', $import_user['email'])->first();
        if($user == null){
            if(!isset($data['ticket_id'])){
                        loging("SpiceworkMigration", "Could not find ticket_id  (import_id: '".$data['created_by']."'')  (body: '".$data['body']."')", "error");
                        return "Migration skipped:-  missing ticket_id";
                }
            loging("SpiceworkMigration", "Could not find user  (import_id: '".$data['created_by']."'')  (ticket_id: '".$data['ticket_id']."')", "error");
            return "Migration skipped";
            //\Log::error("Could not find user  (import_id: '".$data['created_by']."'')  (ticket_id: '".$data['ticket_id']."')");
        }

        if($thread = $thread->where('ticket_id', $ticket->id)->where('created_at', \Carbon\Carbon::parse($data['created_at']))->first()){
            $thread->update([
                    "user_id"    => $user->id,
                    "ticket_id"  => $ticket['id'],
                   // "body"          => str_replace("\n","<br>",$data['body']),
                    "body"          => $data['body'],
                    "created_at"    => carbon($data['created_at']),
                    'is_internal' => $this->internal($thread),
                ]);

            \Log::info("Updated Thread:-    ".$thread."    -------------------------end--------------------------");
        }
        else{
            $this->thread($ticket,array($data), $subject);
        }
    }


    public function internal($ticket)
    {
        $is_internal = 0;
        if (checkArray('is_public', $ticket)==false) {
            $is_internal = 1;
        }
        return $is_internal;
    }
}
