<?php

namespace App\Plugins\Migration\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\Migration\Controllers\ImportCsv;
use Input;
use Hash;

class OsTicketMigration extends Controller
{
    public function import()
    {
        $only_user = true;
        $file      = storage_path('os_ticket.csv'); //Input::get('file');
        if (is_file($file)) {
            $skip = Input::get('skip', 0);
            $take = Input::get('take', 20);
            try {

                $is_sheet = \Excel::load($file, function($reader) use($skip, $take, $only_user) {
                            $raws  = $reader->skipRows($skip)->takeRows($take);
                            $count = $raws->get()->count();
                            if ($count == 0) {
                                $response = ['message' => 'success', 'url' => null];
                                echo json_encode($response);
                                die();
                            }
                            return $reader->each(function($sheet) use($count, $only_user) {
                                        $requester = $this->createRequester($sheet);
                                        if (!$only_user) {
                                            $staff = $this->createStaff($sheet);
                                            $this->createTicket($sheet, $requester, $staff);
                                        }
                                    });
                        });
                $next_skip = $skip + 20;
                if ($is_sheet) {
                    $response = ['completed' => $next_skip, 'url' => url('migration/migrate?skip=' . $next_skip . '&take=' . $take . '&app=osticket')];
                }
                else {
                    $response = ['message' => 'success', 'url' => null];
                }
                return response()->json(compact('response'));
            } catch (\Exception $ex) {
                $response = ['message' => $ex->getMessage(), 'error' => true];
                return response()->json(compact('response'), 500);
            }
        }
        else {
            $response = ['message' => $file . ' not found', 'error' => true];
            return response()->json(compact('response'), 404);
        }
    }
    public function createRequester($raw)
    {
        $email      = (isset($raw->requester_email)) ? $raw->requester_email : (isset($raw->email))
                            ? $raw->email : null;
        $first_name = (isset($raw->requester_name)) ? $raw->requester_name : (isset($raw->first_name))
                            ? $raw->first_name : null;
        $last_name  = (isset($raw->last_name) && $raw->last_name) ? $raw->last_name
                    : "";
        if ($email) {
            $str       = 'password'; //str_random(8);
            $role      = ($raw->role) ? $raw->role : 'user';
            $mobile    = ($raw->mobile) ? $raw->mobile : null;
            $requester = \App\User::updateOrCreate(
                            ['email' => $email,], [

                        'first_name' => $first_name,
                        'user_name'  => $email,
                        'last_name'  => $last_name,
                        'role'       => $role,
                        'active'     => '1',
                        'mobile'     => $mobile,
                        'password'   => Hash::make($str)]);
            return $requester;
        }
    }
    public function createStaff($raw)
    {
        if ($raw->staff_email !== 'NULL') {
            $str   = str_random(8);
            $staff = \App\User::updateOrCreate(
                            [
                        'email' => $raw->staff_email,
                            ], [
                        'first_name' => $raw->staff_firstname,
                        'last_name'  => $raw->staff_lastname,
                        'user_name'  => $raw->staff_username,
                        'role'       => 'agent',
                        'active'     => '1',
                        'agent_sign' => $raw->staff_signature,
                        'password'   => Hash::make($str)
            ]);
            return $staff;
        }
    }
    public function createTicket($raw, $user, $staff)
    {

        $prefix      = "OST-";
        $user_id     = "";
        $assigned_to = "";
        if ($user) {
            $user_id = $user->id;
        }
        if ($staff) {
            $assigned_to = $staff->id;
        }

        $dept_id         = $this->department($raw);
        $tickets         = new \App\Model\helpdesk\Ticket\Tickets();
        $tickets->notify = false;
        $est_duedate     = NULL;
        if ($raw->duedate !== 'NULL') {
            $duedate     = str_replace("/", "-", $raw->duedate);
            $est_duedate = createCarbon($duedate, 'UTC', 'Y-m-d H:m:i');
        }
        $ticket = $tickets->updateOrCreate(
                [
            'ticket_number' => $prefix . $raw->number,
                ], [
            'duedate'       => $est_duedate,
            'user_id'       => $user->id,
            'assigned_to'   => $assigned_to,
            'sla'           => $this->sla(),
            'priority_id'   => $this->priority(),
            'status'        => $this->status($raw),
            'type'          => $this->type(),
            'source'        => $this->source(),
            'dept_id'       => $dept_id,
            'help_topic_id' => $this->helpTopic($dept_id),
                ]
        );
        $this->thread($ticket, $raw);
        return $ticket;
    }
    public function status($raw)
    {
        $status_id = NULL;
        $statuses  = new \App\Model\helpdesk\Ticket\Ticket_Status();
        $status    = $statuses->type()->where('name', $raw->status)->select('id')->first();
        if (!$status) {
            $status = $statuses->select('id')->first();
        }
        if ($status) {
            $status_id = $status->id;
        }
        return $status_id;
    }
    public function department($raw)
    {
        $dept_id    = 1;
        $department = "";
        if ($raw->department_name !== NULL) {
            $department = \App\Model\helpdesk\Agent\Department::updateOrCreate([
                        'name' => $raw->department_name,
                            ], ['department_sign' => $raw->department_signature, 'type' => '1']);
        }
        if ($department) {
            $dept_id = $department->id;
        }
        return $dept_id;
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
    public function thread($ticket, $raw)
    {
        $ticket->thread()->create([
            'user_id'     => $this->threadUserId($raw),
            'poster'      => $this->poster($raw),
            'body'        => $raw->body,
            'title'       => $this->subject($ticket, $raw),
            'is_internal' => $this->internal($raw),
        ]);
    }
    public function poster($raw)
    {
        $poster = 'client';
        if ($raw->thread_staff > 0) {
            $poster = 'support';
        }
        return $poster;
    }
    public function threadUserId($raw)
    {

        $user_id = NULL;
        if ($raw->thread_staff_user_name !== 'NULL') {
            $user_id = $this->threadStaffId($raw);
        }
        elseif ($raw->requester_email) {
            $user = \App\User::updateOrCreate(
                            ['email' => $raw->requester_email], [
                        'user_name'  => $raw->requester_email,
                        'password'   => Hash::make(str_random(8)),
                        'active'     => 1,
                        'role'       => 'user',
                        'agent_sign' => "",
                            ]
            );
            if ($user) {
                $user_id = $user->id;
            }
        }
        return $user_id;
    }
    public function threadStaffId($raw)
    {
        $user_id = NULL;
        if ($raw->thread_staff_user_name !== 'NULL') {
            $user = \App\User::updateOrCreate(
                            ['email' => $raw->thread_staff_email,], [
                        'user_name'  => $raw->thread_staff_email,
                        'password'   => Hash::make(str_random(8)),
                        'active'     => 1,
                        'role'       => 'agent',
                        'agent_sign' => "",
                            ]
            );
            if ($user) {
                $user_id = $user->id;
            }
        }
        return $user_id;
    }
    public function subject($ticket, $raw)
    {
        $title  = NULL;
        $thread = $ticket->thread()->where(function($query) {
                            return $query->whereNotNull('title')
                                    ->orWhere('title', '!=', '');
                        })
                        ->select('id')->first();
        if (!$thread) {
            $title = $raw->subject;
        }
        return $raw->subject;
    }
    public function internal($raw)
    {
        $is_internal = 0;
        if ($raw->thread_type && $raw->thread_type == 'N') {
            $is_internal = 1;
        }
        return $is_internal;
    }
}
