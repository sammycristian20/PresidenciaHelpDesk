<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Controller;
use Plupload;
use Hash;
use Exception;

class ImportController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    public function import() {
        $filename = Plupload::receive('file', function ($file) {
                    $file->move(storage_path() . '/csv/', $file->getClientOriginalName());
                    return $file->getClientOriginalName();
                    //$this->process(storage_path() . '/csv/' .$file->getClientOriginalName());

                });
        //dd($filename['result']);
        if ($filename['result']) {
            $this->process(storage_path() . '/csv/' . $filename['result']);
        }
    }

    public function getImport() {
        return view('themes.default1.admin.helpdesk.import.get-import');
    }

    public function process($file) {
        \Excel::filter('chunk')->load($file)->chunk(250, function($results) {

            foreach ($results as $row) {
                $this->processData($row);
            }
        });
    }

    public function processData($collection) {
        \DB::connection()->disableQueryLog();
        if ($collection->has('email')) {
            $this->insertUser($collection);
        }
        if ($collection->has('body')) {
            $this->insertTicket($collection);
        }
    }

    public function checkHas($collection, $element, $flag = false) {
        $value = "";
        if ($collection->has($element)) {
            $value = $collection->$element;
        } elseif ($flag == true) {
            throw new Exception("$element required for import");
        }
        return $value;
    }

    public function insertUser($collection) {
        try {
            $user = new \App\User();
            $user->user_name = $this->userName($this->checkHas($collection, "user_name", true));
            $user->first_name = $this->checkHas($collection, "first_name");
            $user->last_name = $this->checkHas($collection, "last_name");
            $user->email = $this->checkHas($collection, "email");
            $user->password = $this->password();
            $user->active = $this->checkHas($collection, "active");
            $user->ext = $this->checkHas($collection, "ext");
            $user->country_code = $this->checkHas($collection, "country_code");
            $user->phone_number = $this->checkHas($collection, "phone_number");
            $user->mobile = $this->checkHas($collection, "mobile");
            $user->agent_sign = $this->checkHas($collection, "agent_sign");
            $user->account_type = $this->checkHas($collection, "account_type");
            $user->account_status = $this->checkHas($collection, "account_status");
            $user->assign_group = $this->checkHas($collection, "assign_group");
            $user->primary_dpt = $this->department($this->checkHas($collection, "primary_dpt"), $this->checkHas($collection, "role"));
            $user->agent_tzone = $this->checkHas($collection, "agent_tzone");
            $user->daylight_save = $this->checkHas($collection, "daylight_save");
            $user->limit_access = $this->checkHas($collection, "limit_access");
            $user->directory_listing = $this->checkHas($collection, "directory_listing");
            $user->vacation_mode = $this->checkHas($collection, "vacation_mode");
            //$user->company = $this->checkHas($collection,"company");
            $user->role = $this->checkHas($collection, "role");
            $user->internal_note = $this->checkHas($collection, "internal_note");
            $user->profile_pic = $this->checkHas($collection, "profile_pic");
            $user->not_accept_ticket = $this->checkHas($collection, "not_accept_ticket");
            $user->save();
        } catch (Exception $ex) {
            loging("Import-User", $ex->getMessage());
        }
    }

    public function insertTicket($collection) {
        try {
            $tickets = new \App\Model\helpdesk\Ticket\Tickets();
            $tickets->ticket_number = $this->ticketNumber($this->checkHas($collection, "ticket_number"));
            $tickets->user_id = $this->userid($this->checkHas($collection, "user_name", true));
            $tickets->dept_id = $this->defaultDepartment($this->checkHas($collection, "department"));
            $tickets->team_id = $this->defaultTeam($this->checkHas($collection, "team"));
            $tickets->priority_id = $this->defaultPriority($this->checkHas($collection, "priority"));
            $tickets->sla = $this->defaultSla($this->checkHas($collection, "sla"));
            $tickets->help_topic_id = $this->defaultHelptopic($this->checkHas($collection, "helptopic"));
            $tickets->status = $this->status($this->checkHas($collection, "status"));
            $tickets->assigned_to = $this->assigned($this->checkHas($collection, "agent_user_name"));
            $tickets->source = $this->source($this->checkHas($collection, "source"));
            $tickets->duedate = $this->source($this->checkHas($collection, "duedate"));
            $tickets->save();
            $this->insertThread($tickets->id, $tickets->user_id, $collection);
        } catch (Exception $ex) {
            // dd($ex);
            loging("Import-Ticket", $ex->getMessage());
        }
    }

    public function insertThread($ticketid, $userid, $collection) {
        try {
            $thread = new \App\Model\helpdesk\Ticket\Ticket_Thread();
            $thread->ticket_id = $ticketid;
            $thread->user_id = $userid;
            $thread->poster = 'client';
            $thread->title = $this->checkHas($collection, "subject");
            $thread->body = $this->checkHas($collection, "body", true);
            $thread->save();
        } catch (Exception $ex) {
            loging("Import-thread", $ex->getMessage());
        }
    }

    public function password() {
        $str = str_random(6);
        $hash = Hash::make($str);
        return $hash;
    }

    public function userName($username) {
        $user = \App\User::where('user_name', $username)->select('id')->first();
        if ($user) {
            throw new Exception("$username has already taken");
        }
    }

    public function department($department, $role = "agent") {
        $id = NULL;
        if ($role && $role != "user") {
            $department = \App\Model\helpdesk\Agent\Department::where('name', $department)->select('id')->first();
            if ($department) {
                $id = $department->id;
            }
        }
        return $id;
    }

    public function ticketNumber($ticket_number) {
        if (!$ticket_number) {
            $ticket_settings = new \App\Model\helpdesk\Settings\Ticket();
            $setting = $ticket_settings->select('num_format', 'num_sequence')->first();
            $format = $setting->num_format;
            $type = $setting->num_sequence;
            $ticket_number = $this->getNumber($ticket_number, $type, $format);
        }
        return $ticket_number;
    }

    public function getNumber($ticket_number, $type, $format, $check = true) {
        $force = false;
        if ($check === false) {
            $force = true;
        }
        $controller = new \App\Http\Controllers\Admin\helpdesk\SettingsController();
        if ($ticket_number) {
            $number = $controller->nthTicketNumber($ticket_number, $type, $format, $force);
        } else {
            $number = $controller->switchNumber($format, $type);
        }
        $number = $this->generateTicketIfExist($number, $type, $format);
        return $number;
    }

    public function generateTicketIfExist($number, $type, $format) {
        $tickets = new \App\Model\helpdesk\Ticket\Tickets();
        $ticket = $tickets->where('ticket_number', $number)->select('id')->first();
        if ($ticket) {
            $number = $this->getNumber($number, $type, $format, false);
        }
        return $number;
    }

    public function userid($username) {
        $userid = NULL;
        $user = \App\User::where('user_name', $username)->select('id')->first();
        if ($user) {
            $userid = $user->id;
        } else {
            $user = \App\User::create(['user_name' => $username, 'password' => $this->password(), 'active' => 1,'role'=>'user']);
            $userid = $user->id;
        }
        return $userid;
    }

    public function defaultDepartment($department) {
        if (!$department) {
            $systems = new \App\Model\helpdesk\Settings\System();
            $system = $systems->select('department')->first();
            $department_id = $system->department;
        } else {
            $department_id = $this->department($department);
        }
        return $department_id;
    }

    public function defaultTeam($team) {
        $team_id = NULL;
        if ($team) {
            $teams = \App\Model\helpdesk\Agent\Teams::where('name', $team)->select('id')->first();
            if ($teams) {
                $team_id = $teams->id;
            }
        }
        return $team_id;
    }

    public function defaultPriority($priority_name) {
        $priority_id = NULL;
        if ($priority_name) {
            $proirities = \App\Model\helpdesk\Ticket\Ticket_Priority::where('priority', $priority_name)->select('priority_id')->first();
            if ($proirities) {
                $priority_id = $proirities->priority_id;
            }
        }
        if (!$priority_id) {
            $priority_id = $this->getSystemDefaultPriority();
        }
        return $priority_id;
    }

    public function getSystemDefaultPriority() {
        $ticket_settings = new \App\Model\helpdesk\Settings\Ticket();
        $ticket_setting = $ticket_settings->select('priority')->first();
        $priority = $ticket_setting->priority;
        return $priority;
    }

    public function defaultSla($sla) {
        $sla_id = NULL;
        if ($sla) {
            $slas = \App\Model\helpdesk\Manage\Sla\Sla_plan::where('name', $sla)->select('id')->first();
            if ($slas) {
                $sla_id = $slas->id;
            }
        }
        if (!$sla_id) {
            $sla_id = $this->getSystemDefaultSla();
        }
        return $sla_id;
    }

    public function getSystemDefaultSla() {
        $ticket_settings = new \App\Model\helpdesk\Settings\Ticket();
        $ticket_setting = $ticket_settings->select('sla')->first();
        $sla = $ticket_setting->sla;
        return (int) $sla;
    }

    public function defaultHelptopic($topic) {
        $helptopic_id = NULL;
        if ($topic) {
            $helptopic = \App\Model\helpdesk\Manage\Help_topic::where('topic', $topic)->select('id')->first();
            $helptopic_id = $helptopic->id;
        }
        if (!$helptopic_id) {
            $helptopic_id = $this->getSystemDefaultHelpTopic();
        }
        return $helptopic_id;
    }

    public function getSystemDefaultHelpTopic() {
        $ticket_settings = new \App\Model\helpdesk\Settings\Ticket();
        $ticket_setting = $ticket_settings->select('help_topic')->first();
        $help_topicid = $ticket_setting->help_topic;
        return $help_topicid;
    }

    public function status($status) {
        $status_id = NULL;
        if (!$status) {
            $status = "closed";
        }
        $statuses = \App\Model\helpdesk\Ticket\Ticket_Status::where('name', $status)->select('id')->first();
        if ($statuses) {
            $status_id = $statuses->id;
        }
        return $status_id;
    }

    public function assigned($username) {
        $agent_id = NULL;
        $agents = \App\User::where('user_name', $username)->where('role', '!=', 'user')->select('id')->first();
        if ($agents) {
            $agent_id = $agents->id;
        }
        return $agent_id;
    }

    public function source($source) {
        $source_id = NULL;
        if (!$source) {
            $source = "web";
        }
        $sources = \App\Model\helpdesk\Ticket\Ticket_source::where('name', $source)->select('id')->first();
        if ($sources) {
            $source_id = $sources->id;
        }
        return $source_id;
    }

}
