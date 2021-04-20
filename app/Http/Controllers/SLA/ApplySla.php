<?php

namespace App\Http\Controllers\SLA;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController;
use Exception;
use App\Model\helpdesk\Ticket\Tickets;
use App\User;

class ApplySla extends Controller
{

    public $respose_approach_persons;
    public $resolve_approach_persons;
    public $respose_violate_persons;
    public $resolve_violate_persons;
    public $not_assign_persons;
    public $ticket       = NULL;
    public $sla          = NULL;
    public $due_type     = NULL;
    public $minutes      = NULL;
    public $dept         = NULL;
    public $org_dept     = NULL;
    public $requester_id = NULL;

    /**
     * add persons to this instance
     */
    public function addPersons()
    {
        $this->respose_approach_persons = $this->resposeApproachPersons();
        $this->resolve_approach_persons = $this->resolveApproachPersons();
        $this->respose_violate_persons  = $this->resposeViolatedPersons();
        $this->resolve_violate_persons  = $this->resolutionViolatedPersons();
        //$this->not_assign_persons = $this->notAssignPersons();
    }
    /**
     * get the response approach persons
     * @return array
     */
    public function resposeApproachPersons()
    {
        if ($this->sla->sla) {
            $sla                      = $this->sla->sla;
            $respose_approach_persons = $sla->approach()
                    ->where('escalate_type', 'response')
                    ->select('escalate_person', 'escalate_time')
                    ->pluck('escalate_person', 'escalate_time')
                    ->toArray();
            return $respose_approach_persons;
        }
    }
    /**
     * get the resolve appraoch persons
     * @return array
     */
    public function resolveApproachPersons()
    {
        if ($this->sla->sla) {
            $sla                         = $this->sla->sla;
            $resolution_approach_persons = $sla->approach()
                    ->where('escalate_type', 'resolution')
                    ->select('escalate_person', 'escalate_time')
                    ->pluck('escalate_person', 'escalate_time')
                    ->toArray();
            return $resolution_approach_persons;
        }
    }
    /**
     * get the response violate persons
     * @return array
     */
    public function resposeViolatedPersons()
    {
        if ($this->sla->sla) {
            $sla                      = $this->sla->sla;
            $respose_approach_persons = $sla->violated()
                    ->where('escalate_type', 'response')
                    ->select('escalate_person', 'escalate_time')
                    ->pluck('escalate_person', 'escalate_time')
                    ->toArray();
            return $respose_approach_persons;
        }
    }
    /**
     * get the resolve violate persons
     * @return array
     */
    public function resolutionViolatedPersons()
    {
        if ($this->sla->sla) {
            $sla                      = $this->sla->sla;
            $respose_approach_persons = $sla->violated()
                    ->where('escalate_type', 'resolution')
                    ->select('escalate_person', 'escalate_time')
                    ->pluck('escalate_person', 'escalate_time')
                    ->toArray();
            return $respose_approach_persons;
        }
    }
    /**
     * get the not assign persons
     * @return array
     */
    public function notAssignPersons()
    {
        //return ['-30'=>['admin']];
        if ($this->sla->sla) {
            $sla                      = $this->sla->sla;
            $respose_approach_persons = $sla->notAssign()
                    ->where('escalate_type', 'no_assign')
                    ->select('escalate_person', 'escalate_time')
                    ->pluck('escalate_person', 'escalate_time')
                    ->toArray();
            return $respose_approach_persons;
        }
    }
    /**
     * get the sla class
     * @param ineteger $id
     * @return \App\Http\Controllers\SLA\Sla
     */
    public function sla($id)
    {
        $sla = new Sla($id);
        $this->setRequesterDepartment();
        $this->setDepartment($sla);
        return $sla;
    }
    public function setRequesterDepartment()
    {
        if (isMicroOrg() && $this->requester_id) {
            $requester  = User::find($this->requester_id);
            $deaprtment = $requester->org()->first();
            if ($deaprtment && $deaprtment->org_department) {
                $this->org_dept = $deaprtment->org_department;
            }
        }
    }
    public function setDepartment($sla)
    {
        $department = "";
        if (isMicroOrg() && $this->org_dept) {
            $department = \App\Model\helpdesk\Agent_panel\OrganizationDepartment::whereHas('businessHour')
                    ->where('id', $this->org_dept)
                    ->first();
        }

        if ($this->dept && ($department == null || $department == "")) {
            $department = \App\Model\helpdesk\Agent\Department::whereHas('businessHour')
                    ->where('id', $this->dept)
                    ->first();
        }

        if ($department) {
            $sla->business_hour = $department->businessHour;
        }
    }
    /**
     * get sla
     * @param integer $id
     * @return \App\Http\Controllers\SLA\Sla
     * @throws Exception
     */
    public function getSla($id = "")
    {
        if ($id == "") {
            $sla = Sla_plan::first();
            if (!$sla) {
                throw new Exception('SLA not found');
            }
            $id = $sla->id;
        }
        return $this->sla($id);
    }
    /**
     * get default sla
     * @return ineger
     */
    public function defaultSla()
    {
        $sla = $this->getSla();
        return $sla->getId();
    }
    /**
     * get the department sla
     * @param integer $depid
     * @return integer
     */
    public function departmentSla($depid)
    {
        $plans = Sla_plan::select('id')->get();
        if ($plans->count() > 0) {
            foreach ($plans as $plan) {
                $departments = $this->getSla($plan->id)->getDepartments();
                if (is_array($departments) && in_array($depid, $departments)) {
                    return $this->getSla($plan->id)->getId();
                }
            }
        }
    }
    /**
     * get sla with company
     * @param integer $orgId
     * @return integer
     */
    public function companySla($orgId)
    {
        $plans = Sla_plan::select('id')->get();
        if ($plans->count() > 0) {
            foreach ($plans as $plan) {
                $companies = $this->getSla($plan->id)->getCompanies();
                if (is_array($companies) && in_array($orgId, $companies)) {
                    return $this->getSla($plan->id)->getId();
                }
            }
        }
    }
    /**
     * get type in the sla
     * @param integer $typeid
     * @return integer
     */
    public function typeSla($typeid)
    {
        $plans = Sla_plan::select('id')->get();
        if ($plans->count() > 0) {
            foreach ($plans as $plan) {
                $types = $this->getSla($plan->id)->getTicketTypes();
                if (is_array($types) && in_array($typeid, $types)) {
                    return $this->getSla($plan->id)->getId();
                }
            }
        }
    }
    /**
     * get type in the source
     * @param integer $sourceid
     * @return integer
     */
    public function sourceSla($sourceid)
    {
        $plans = Sla_plan::select('id')->get();
        if ($plans->count() > 0) {
            foreach ($plans as $plan) {
                $sources = $this->getSla($plan->id)->getTicketSources();
                if (is_array($sources) && in_array($sourceid, $sources)) {
                    return $this->getSla($plan->id)->getId();
                }
            }
        }
    }
    /**
     * get the ticket instance
     * @param type $ticketid
     * @return Tickets
     * @throws Exception
     */
    public function ticket($ticketid)
    {
        $tickets = new Tickets();
        $ticket  = $tickets->find($ticketid);
        if (!$ticket) {
            throw new Exception('Ticket not found for SLA');
        }
        return $ticket;
    }
    /**
     * get sla respond due date
     * @param integer $slaid
     * @param \Carbon\Carbon $ticket_created
     * @return \Carbon\Carbon
     */
    public function slaRespondsDue($slaid, $ticketCreated, $ticket = "")
    {
        //echo "Created_at from Database => ".$ticketCreated."(UTC)<br>";
        $sla                                    = $this->sla($slaid);
        //echo "SLA name => ".$sla->getName()."<br>";
        $respond                                = $sla->respondTime();
        ($ticket != "") && ($respond = $ticket->halt()->sum('halted_time') + $respond);
        $ticketCreatedWithBussinessTimezone = $ticketCreated->tz($sla->timezone());
        //echo "Ticket Created with Bussiness Hour Time Zone => ".$ticketCreatedWithBussinessTimezone." (".$sla->timezone().")<br>";
        $estimate                               = null;
        if ($respond > 0) {
            $estimate = $this->estimateRespondsDue($ticketCreatedWithBussinessTimezone, $sla, $respond);
        }
        //dd($estimate);
        //echo "Estimated Duedate => ".$estimate."(".$sla->timezone().")<br>";
        //echo "Estimated Duedate => ".$estimate->tz(timezone())."(".timezone().")<br>";
        //echo "Estimated Duedate Saving to database => ".$estimate->tz('UTC')."(UTC)<br>";
        return $estimate;
    }
    /**
     * get the sla resolve duedate
     * @param integer $slaid
     * @param \Carbon\Carbon $ticket_created
     * @param type $resolve
     * @return \Carbon\Carbon
     */
    public function slaResolveDue($slaid, $ticket_created, $resolve = "")
    {
        $sla = $this->sla($slaid);
        if ($resolve == "") {
            $resolve = $sla->resolveTime();
        }
        $ticket_created_with_bussiness_timezone = $ticket_created->tz($sla->timezone());
        $estimate                               = null;
        if ($resolve > 0) {
            //dd($ticket_created->timezone(timezone()),$resolve);
            $estimate = $this->estimateRespondsDue($ticket_created_with_bussiness_timezone, $sla, $resolve);
        }
//        dd($estimate);
        return $estimate;
    }

    /**
     * get the business time
     * @param string $start
     * @param string $end
     * @param integer $slaid
     * @param string $time
     * @return mixed
     */
    public function businessTime($start, $end, $slaid, $time = 0)
    {
        // calculates time difference in business hour
        $sla         = $this->sla($slaid);

        //  NOTE FROM AVINASH : We are not touching "which business hour to enforce" part, only business hour calculation part
        //  while working on SLA, this also has to be rewritten
        // get business hour Id and pass the control to BusinessHourCalculation class after that
        return (new BusinessHourCalculation($sla->business_hour))->getTimeDiffInBH($start, $end);
    }

    /**
     * get the time spent on a ticket
     * @param integer $ticketid
     * @param string $type
     * @return integer
     */
    public function minSpent($ticketid, $type = "response")
    {
        $ticket = $this->ticket($ticketid);
        $sla    = $this->sla($ticket->sla);
        $now    = \Carbon\Carbon::now()->tz($sla->timezone());
        if ($type == "response") {
            $response_time = $sla->respondTime();
            $duetime       = $this->slaRespondsDue($ticket->sla, $ticket->created_at);
        }
        if ($type == "resolve") {
            $response_time = $sla->resolveTime();
            $duetime       = $this->slaResolveDue($ticket->sla, $ticket->created_at);
        }

        // NOTE : should be in business hour
        $difference = $now->diffInMinutes($duetime, false);
        return $response_time - $difference;
    }
    /**
     * send the escalation to users
     */
    public function send()
    {
        $date     = \Carbon\Carbon::now();
        //echo "Now $date <br>";
        $ticket   = new Tickets();
        $status   = new \App\Model\helpdesk\Ticket\Ticket_Status();
        $closedid = $status->join('ticket_status_type', 'ticket_status.purpose_of_status', '=', 'ticket_status_type.id')
                ->where('ticket_status_type.name', '=', 'open')
                ->select('ticket_status.id')
                ->get()
                ->toArray();
        $tickets  = $ticket
                ->whereIn('status', $closedid)
                //->whereDate('duedate', '<', $date)
                ->select('id')
                ->chunk(10, function($tickets) {
            foreach ($tickets as $ticket) {
                $this->sendReport($ticket->id);
            }
            //echo "chunck finished";
        });
    }
    /**
     * send email
     */
    public function sendEmail()
    {
        if ($this->sla->isSendEmail() == true) {
            $due_type = 'response_due';
            if ($this->ticket->isanswered == 1) {
                $due_type = 'resolve_due';
            }
            $this->due_type = $due_type;
            $this->dispatchEmail();

            if ($this->ticket->isanswred == 0 && !$this->ticket->assign_to) {
                $due_type = 'no_assign';
            }
            $this->due_type = $due_type;
            $this->dispatchEmail();
        }
    }
    /**
     * get apprach persons
     * @return array
     */
    public function getApproachesPersons()
    {
        $persons = $this->respose_approach_persons;
        if ($this->due_type == 'resolve_due') {
            $persons = $this->resolve_approach_persons;
        }
        return $persons;
    }
    /**
     * get violate persons
     * @return array
     */
    public function getViolatePersons()
    {
        $persons = $this->respose_violate_persons;
        if ($this->due_type == 'resolve_due') {
            $persons = $this->resolve_violate_persons;
        }
        return $persons;
    }
    /**
     * get assign persons
     * @return array
     */
    public function getNoAssignPersons()
    {
        $persons = [];
        if ($this->due_type == 'no_assign') {
            $persons = $this->not_assign_persons;
        }
        return $persons;
    }
    /**
     * get appraches persons
     * @return array
     */
    public function approachesPersons()
    {
        $persons = $this->getApproachesPersons();
        if (count($persons) > 0) {
            foreach ($persons as $key => $value) {
                $this->minutes = $key;
                if (is_array($value)) {
                    foreach ($value as $person) {
                        $result[$this->minutes][$person] = $this->getField($person);
                    }
                }
            }
            return $result;
        }
    }
    /**
     * get violate persons
     * @return array
     */
    public function violatePersons()
    {
        $persons = $this->getViolatePersons();
        if (count($persons) > 0) {
            foreach ($persons as $key => $value) {
                $this->minutes = $key;
                if (is_array($value)) {
                    foreach ($value as $person) {
                        $result[$this->minutes][$person] = $this->getField($person);
                    }
                }
            }
            return $result;
        }
    }
    /**
     * get no assugned persons
     * @return array
     */
    public function noAssignedPersons()
    {
        $persons = $this->getNoAssignPersons();
        if (count($persons) > 0) {
            foreach ($persons as $key => $value) {
                $this->minutes = $key;
                if (is_array($value)) {
                    foreach ($value as $person) {
                        $result[$this->minutes][$person] = $this->getField($person);
                    }
                }
            }
            return $result;
        }
    }
    /**
     * dispatch emails
     * @return array
     */
    public function dispatchEmail()
    {
        if ($this->due_type && $this->due_type == 'response_due') {
            echo $this->due_type . "<br>";
            $this->dispatchApproachMail();
            $this->dispatchViolateMail();
        }
        if ($this->due_type && $this->due_type == 'resolve_due') {
            echo $this->due_type . "<br>";
            $this->dispatchApproachMail();
            $this->dispatchViolateMail();
        }
//        if ($this->due_type && $this->due_type == 'no_assign') {
//            echo $this->due_type . "<br>";
//            $this->dispatchNoAssignMail();
//        }
    }
    /**
     * disptch approaching emails
     */
    public function dispatchApproachMail()
    {
        $persons = $this->approachesPersons();
        echo json_encode($persons) . "</br>";
        if ($persons) {
            foreach ($persons as $minute => $emails) {
                echo "is array emails => " . is_array($emails) . " " . json_encode($emails) . "<br>";

                if (is_array($emails) && $this->isApproaches($minute)) {
                    loging('sla-approach ticket number: ' . $this->ticket->ticket_number, json_encode($emails), 'info');
                    echo "I have entered <br>";
                    $diff_array = [];
                    foreach ($emails as $person => $email) {
                        echo "is array email => " . is_array($email) . " " . json_encode($email) . "<br>";
                        if (is_array($email)) {
                            if (count($diff_array) == 0) {
                                foreach ($email as $name => $e_mail) {
                                    echo $minute . " => approach =>" . $person . " => " . $name . " => " . $e_mail . "<br>";
                                    $this->sendMail($e_mail, $name, 'approach');
                                    $this->sendSms($e_mail, $name, 'approach');
                                    $this->push($e_mail, $name, 'approach');
                                    array_push($diff_array, $e_mail);
                                }
                            }
                            else {
                                foreach ($email as $name => $e_mail) {
                                    if (!in_array($e_mail, $diff_array)) {
                                        echo $minute . " => approach =>" . $person . " => " . $name . " => " . $e_mail . "<br>";
                                        $this->sendMail($e_mail, $name, 'approach');
                                        $this->sendSms($e_mail, $name, 'approach');
                                        $this->push($e_mail, $name, 'approach');
                                        array_push($diff_array, $e_mail);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * check ticket is aproaching 
     * @param integer $minute
     * @return boolean
     */
    public function isApproaches($minute)
    {
        $check = false;
        $now   = \Carbon\Carbon::now();
        echo "Now => " . $now . "<br>";
        if ($this->ticket && $this->ticket->duedate) {
            echo "Minutes => " . $minute . "<br>";
            $due_actual      = $this->ticket->duedate;
            $due_second      = $this->ticket->duedate;
            echo "Approach due => " . $due_actual . "<br>";
            $due_add_minutes = $due_actual->addMinutes($minute);
            $now_plus_30     = \Carbon\Carbon::now()->addMinutes(30);
            echo "Approach added minutes=> " . $due_add_minutes . "<br>";
            echo $due_add_minutes . "(Approach added minutes) >= " . $now . "(now) && " . $due_second . "(Approach due) > " . $now . "(now) && " . $due_add_minutes . "(Approach added minutes) < " . $now_plus_30 . " (now +30)<br>-------<br><br><br><br><br><br>";
            if ($due_add_minutes >= $now && $due_second > $now && $due_add_minutes
                    < $now_plus_30) {
                echo "true<br>";
                $check = true;
            }
        }

        return $check;
    }
    /**
     * dispatch email for unassigned
     */
    public function dispatchNoAssignMail()
    {
        $persons = $this->noAssignedPersons();
        echo json_encode($persons) . "</br>";
        if ($persons) {
            foreach ($persons as $minute => $emails) {
                echo "is array emails => " . is_array($emails) . " " . json_encode($emails) . "<br>";
                if (is_array($emails) && $this->isNoAssignApproaches($minute)) {
                    echo "I have entered <br>";
                    $diff_array = [];
                    foreach ($emails as $person => $email) {
                        echo "is array email => " . is_array($email) . " " . json_encode($email) . "<br>";
                        if (is_array($email)) {
                            if (count($diff_array) == 0) {
                                foreach ($email as $name => $e_mail) {
                                    echo $minute . " => approach =>" . $person . " => " . $name . " => " . $e_mail . "<br>";
                                    $this->sendMail($e_mail, $name, 'message');
                                    $this->sendSms($e_mail, $name, 'message');
                                    array_push($diff_array, $e_mail);
                                }
                            }
                            else {
                                foreach ($email as $name => $e_mail) {
                                    if (!in_array($e_mail, $diff_array)) {
                                        echo $minute . " => approach =>" . $person . " => " . $name . " => " . $e_mail . "<br>";
                                        $this->sendMail($e_mail, $name, 'message');
                                        $this->sendSms($e_mail, $name, 'message');
                                        array_push($diff_array, $e_mail);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function isNoAssignApproaches($minute)
    {
        $now                          = \Carbon\Carbon::now();
        $check                        = false;
        $now_plus_minutes             = \Carbon\Carbon::now()->addMinutes($minute);
        $created_plus_30              = $this->ticket->created_at->addMinutes(30);
        $created_plus_minutes         = $this->ticket->created_at->addMinutes($minute);
        $created_plus_minutes_plus_30 = $this->ticket->created_at->addMinutes(30);
        $ticket_created_time          = $this->ticket->created_at;
        //echo "$ticket_created_time>=$now_plus_minutes && $ticket_created_time<$created_plus_30<br>";
        if ($created_plus_minutes <= $now && $created_plus_minutes_plus_30 >= $now) {
            $check = true;
        }
        return $check;
    }
    public function dispatchViolateMail()
    {
        $persons = $this->violatePersons();
        echo json_encode($persons) . "</br>";
        if ($persons) {
            foreach ($persons as $minute => $emails) {
                echo "is array emails => " . is_array($emails) . " " . json_encode($emails) . "<br>";
                if (is_array($emails) && $this->isViolated($minute)) {
                    $diff_array = [];
                    foreach ($emails as $person => $email) {
                        loging('sla-violate ticket number: ' . $this->ticket->ticket_number, json_encode($emails), 'info');
                        echo "is array email => " . is_array($email) . " " . json_encode($email) . "<br>";
                        if (is_array($email)) {
                            if (count($diff_array) == 0) {
                                foreach ($email as $name => $e_mail) {
                                    $this->sendMail($e_mail, $name, 'violate');
                                    $this->sendSms($e_mail, $name, 'violate');
                                    $this->push($e_mail, $name, 'violate');
                                    echo $minute . " => violated =>" . $person . " => " . $name . " => " . $e_mail . "<br>";
                                    array_push($diff_array, $e_mail);
                                }
                            }
                            else {
                                foreach ($email as $name => $e_mail) {
                                    if (!in_array($e_mail, $diff_array)) {
                                        $this->sendMail($e_mail, $name, 'violate');
                                        $this->sendSms($e_mail, $name, 'violate');
                                        $this->push($e_mail, $name, 'violate');
                                        echo $minute . " => violated =>" . $person . " => " . $name . " => " . $e_mail . "<br>";
                                        array_push($diff_array, $e_mail);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    /**
     * check ticket is violated
     * @param integer $minute
     * @return boolean
     */
    public function isViolated($minute)
    {
        $check = false;
        $now   = \Carbon\Carbon::now();
        echo "Now => " . $now . "<br>";
        if ($this->ticket && $this->ticket->duedate) {
            echo "Minutes => " . $minute . "<br>";
            $due_actual            = $this->ticket->duedate;
            $due_second            = $this->ticket->duedate;
            echo "Violate due => " . $due_actual . "<br>";
            $due_add_minutes       = $due_actual->addMinutes($minute);
            echo "violate added minutes=> " . $due_add_minutes . "<br>";
            $half_minutes          = 30;
            $due_add_minutes_extra = $due_second->addMinutes($minute)->addMinutes(30);
            echo "violate added extra minutes=> " . $due_add_minutes_extra . "<br>";
            echo $due_add_minutes . "(violate added minutes) <= " . $now . "(now) && " . $due_add_minutes_extra . "(violate added extra 30 minutes) > " . $now . "(now) <br>-------<br><br><br><br>";
            if ($due_add_minutes <= $now && $due_add_minutes_extra > $now) {
                $check = true;
            }
        }

        return $check;
    }
    /**
     * get the users details
     * @param string $person
     * @param string $field
     * @param boolean $schma
     * @return mixed
     */
    public function getField($person, $field = "email", $schma = true)
    {
        $collection = collect();
        $collection->push($this->getAgentIdByDependency($person));
        $unique     = $collection->flatten()->unique()->filter(function ($item) {
            return $item != null;
        });
        if ($schma == true) {
            $unique = \App\User::where('is_delete', '!=', '1')
                            ->whereNotNull($field)->whereIn('id', $unique)->pluck($field, 'first_name')->toArray();
        }
        return $unique;
    }
    /**
     * get escalated users
     * @param string $person
     * @return array
     */
    public function getAgentIdByDependency($person)
    {
        $agents = [];
        switch ($person) {
            case "department_members": // pass department id
                if ($this->ticket) {
                    $modelid = $this->ticket->dept_id;
                    $agents  = \App\Model\helpdesk\Agent\DepartmentAssignAgents::where('department_id', $modelid)->select('agent_id as department_members')->get()->toArray();
                }
                return $agents;
            case "team_members": //pass team id
                if ($this->ticket) {
                    $modelid = $this->ticket->team_id;
                    $agents  = \App\Model\helpdesk\Agent\Assign_team_agent::where('team_id', $modelid)->select('agent_id as team_members')->get()->toArray();
                }
                return $agents;
            case "agent":
                $agents = \App\User::where('role', 'agent')->select('id')->get()->toArray();
                return $agents;
            case "admin":
                $agents = \App\User::where('role', 'admin')->select('id as admin')->get()->toArray();
                return $agents;
            case "user": // pass ticket user id
                if ($this->ticket) {
                    $modelid = $this->ticket->user_id;
                    $agents  = ['user' => $modelid];
                }
                return $agents;
            case "agent_admin":
                $agents = \App\User::where('role', '!=', 'user')->select('id as agent_admin')->get()->toArray();
                return $agents;
            case "department_manager"://pass department id
                if ($this->ticket) {
                    $modelid = $this->ticket->dept_id;

                    $agents= \App\Model\helpdesk\Agent\DepartmentAssignManager::where('department_id', $modelid)->select('manager_id as department_manager')->get()->toArray();


                    // $agents  = \App\Model\helpdesk\Agent\Department::where('id', $modelid)->select('manager as department_manager')->get()->toArray();
                }
                return $agents;
            case "team_lead": //pass team id
                if ($this->ticket) {
                    $modelid = $this->ticket->team_id;
                    $agents  = \App\Model\helpdesk\Agent\Teams::where('id', $modelid)->where('status', 1)->select('team_lead as team_lead')->get()->toArray();
                }
                return $agents;
            case "organization_manager"://pass user id
                if ($this->ticket) {
                    $modelid = $this->ticket->user_id;
                }
                else {
                    $modelid = $this->userid;
                }
                if ($modelid) {
                    $org = \App\Model\helpdesk\Agent_panel\User_org::where('user_id', $modelid)->select('org_id')->first();
                    if ($org) {
                        $orgid  = $org->org_id;
                        $agents = \App\Model\helpdesk\Agent_panel\Organization::where('id', $orgid)->select('head as organization_manager')->get()->toArray();
                    }
                }
                return $agents;
            case "last_respondent":
                if ($this->ticket) {
                    $agents = $this->ticket->thread()->whereNotNull('user_id')->orderBy('id', 'desc')->select('user_id as last_respondent')->first()->toArray();
                }
                return $agents;
            case "assigned_agent_team":
                if ($this->ticket) {
                    $agents = ['assigned_agent_team' => $this->ticket->assigned_to];
                }
                return $agents;
            case "assigner":
                if ($this->ticket) {
                    $agents = ['assigned_agent_team' => $this->ticket->assigned_to];
                }
                return $agents;
            case "all_department_manager":
                $agents = \App\Model\helpdesk\Agent\Department::select('manager as all_department_manager')->get()->toArray();
                return $agents;
            case "all_team_lead":
                $agents = \App\Model\helpdesk\Agent\Teams::where('status', 1)->select('team_lead as all_team_lead')->get()->toArray();
                return $agents;
            case "client":
                if ($this->ticket) {
                    $agents = ['client' => $this->ticket->user_id];
                }
                return $agents;
            default :
                if ($person) {
                    $agents = \App\User::where('id', $person)->select('id')->get()->toArray();
                    return $agents;
                }
        }
    }
    /**
     * send report as email
     * @param ineteger $ticketid
     */
    public function sendReport($ticketid)
    {
        try {
            $ticket       = $this->ticket($ticketid);
            echo "Ticket : " . $ticket->id . "<br>";
            //echo "Sla : " . $ticket->sla . "<br>";
            $this->ticket = $ticket;
            $slaid        = $ticket->sla;

            if ($slaid) {
                $sla       = $this->sla($slaid);
                $this->sla = $sla;
            }
            $this->addPersons();
            $this->sendEmail();
            //echo "<hr>";
        } catch (\Exception $ex) {
            loging('sla-escaltion', $ex->getMessage());
        }
    }
    /**
     * send email
     * @param string $email
     * @param string $name
     * @param string $condition
     */
    public function sendMail($email, $name, $condition)
    {
        try {

            $emailCheck = User::where('email', $email)->where('active', 1)->where('is_delete', '!=', 1)->first();
            $email=($emailCheck)?$email:null;
            $userExtraDetails = User::where('email', $email)->select('user_language', 'role')->first();
            $type               = $this->due_type;
            $scenario           = $type . "_" . $condition;
            $templateVariables = $this->getTemplateVariables();
            echo "<b>$scenario</b><br>";
            $phpMail            = new \App\Http\Controllers\Common\PhpMailController();
            $from               = $phpMail->mailfrom('1', $this->ticket->dept_id);
            $to                 = ['email' => $email, 'name' => $name, 'preferred_language' => $userExtraDetails->user_language, 'role' => $userExtraDetails->role];
            $message            = ['scenario' => $scenario];
            $encoded            = json_encode(['from' => $from, 'to' => $to, 'message' => $message, 'template_variables' => $templateVariables]) . "<br>";
            $phpMail->sendmail($from, $to, $message, $templateVariables);
            echo "<b>Sent to $email</b><br>";
            loging('sla-escaltion', $encoded, 'info');
        } catch (\Exception $e) {
            
        }
    }
    /**
     * get all template variable
     * @return array
     */
    public function getTemplateVariables()
    {
        $client_name    = '';
        $client_email   = '';
        $client_contact = '';
        $agent_email    = '';
        $agent_name     = '';
        $agent_contact  = '';
        $requester      = $this->ticket->user;
        $assign_agent   = $this->ticket->assigned;
        $ticketid       = $this->ticket->id;
        if ($requester) {
            $client_name    = ($requester->first_name != '' || $requester->last_name
                    != null) ? $requester->first_name . ' ' . $requester->last_name
                        : $requester->user_name;
            $client_email   = $requester->email;
            $client_contact = $requester->mobile;
        }
        if ($assign_agent) {
            $agent_email   = $assign_agent->email;
            $agent_name    = ($assign_agent->first_name != '' || $assign_agent->last_name
                    != null) ? $assign_agent->first_name . ' ' . $assign_agent->last_name
                        : $assign_agent->user_name;
            $agent_contact = $assign_agent->mobile;
        }
        $template_variables = [

            'ticket_due_date'   => faveoDate($this->ticket->duedate->tz(timezone())),
            'ticket_subject'    => title($ticketid),
            'ticket_number'     => $this->ticket->ticket_number,
            'ticket_link'       => faveoUrl('thread/' . $ticketid),
            'ticket_created_at' => $this->ticket->created_at->tz(timezone()),
            'agent_name'        => $agent_name,
            'agent_email'       => $agent_email,
            'agent_contact'     => $agent_contact,
            'client_email'      => $client_email,
            'client_name'       => $client_name,
            'client_contact'    => $client_contact,
        ];

        return $template_variables;
    }

    /**
     * send sla sms escalation
     * @param string $email
     * @param string $name
     * @param string $condition
     */
    public function sendSms($email, $name, $condition)
    {
        try {
            if ($this->sla->isSendSms() == true) {
                $notification = new NotificationController();
                if ($notification->checkPluginSetup()) {
                    $user_details = User::select('email', 'first_name', 'last_name', 'user_name', 'role', 'mobile', 'country_code', 'user_language')->where('email', '=', $email)->where('mobile', '!=', null)->first()->toArray();
                    if (count($user_details) > 0) {
                        $type               = $this->due_type;
                        $scenario           = $type . "_" . $condition;
                        $template_variables = $this->getTemplateVariables();
                        $message            = ['scenario' => $scenario];
                        $ticket             = $this->ticket->toArray();
                        $sms_controller     = new \App\Plugins\SMS\Controllers\MsgNotificationController;
                        $sms_controller->notifyBySMS($user_details, $template_variables, $message, $ticket);
                    }
                }
            }
        } catch (\Exception $e) {
//            dd($e);
        }
    }
    public function ticketTime($sla_id, $start_date, $end_date, $value = 0)
    {
        $sla      = $this->sla($sla_id);

        //  NOTE FROM AVINASH : We are not touching "which business hour to enforce" part, only business hour calculation part
        //  while working on SLA, this also has to be rewritten
        // get business hour Id and pass the control to BusinessHourCalculation class after that
        return (new BusinessHourCalculation($sla->business_hour))->getTimeDiffInBH($start_date, $end_date);
    }

    public function estimateRespondsDue($created_at, $sla, $respond_time, $n = 1,$min=0)
    {
        if ($respond_time <= 0) {
            return $created_at;
        }

        //  NOTE FROM AVINASH : We are not touching "which business hour to enforce" part, only business hour calculation part
        //  while working on SLA, this also has to be rewritten
        // get business hour Id and pass the control to BusinessHourCalculation class after that
        return (new BusinessHourCalculation($sla->business_hour))->getDueDate($created_at, $respond_time * 60);
    }
    
    public function open($sla_id, $start_date, $end_date)
    {
        //find the minutes between created date and today end time
        //if end_date is less than today's end time consider end_date not today end time
        $today_end_time = (\Carbon\Carbon::today()->setTime(23, 59, 59)->gt($end_date))
                    ?
                $end_date : \Carbon\Carbon::today()->setTime(23, 59, 59);
        $min            = $start_date->diffInMinutes($today_end_time, false);
        return $this->ticketTime($sla_id, $start_date->tomorrow(), $end_date, $min);
    }
    public function getArray($sla_id, $start_date, $end_date, $schedule)
    {
        $timezone   = $this->sla($sla_id)->timezone();
        $open       = explode(":", $schedule[0]['open_time']);
        $end        = explode(":", $schedule[0]['close_time']);
        $open_hour  = $open[0];
        $open_min   = $open[1];
        $end_hour   = $end[0];
        $end_min    = $end[1];
        $start_date = $start_date->tz($timezone);
        $end_date   = $end_date->tz($timezone);
        $open_date  = $start_date->copy()->setTime($open_hour, $open_min, 0);
        $close_date = $start_date->copy()->setTime($end_hour, $end_min, 0);
        //if start date less than open time consider open time
        $work_start = ($start_date->lt($open_date)) ? $open_date : $start_date;
        //if end date greater than close date consider close date
        $stop_work  = ($end_date->gt($close_date)) ? $close_date : $end_date;
        $min        = $work_start->diffInMinutes($stop_work, false);
        return $this->ticketTime($sla_id, $work_start->tomorrow(), $stop_work, $min);
    }
    public function push($email, $name, $condition)
    {
        try {
            if ($this->sla->isPush() == true) {
                $message_condition   = ($this->due_type == 'response_due') ? "Response due"
                            : "Resolution due";
                $message_type        = ($condition == 'approach') ? "approaching"
                            : 'violated';
                $number              = $this->ticket->ticket_number;
                $to                  = User::whereEmail($email)->value('id');
                $message             = trans('lang.sla_inapp_notification', ['condition' => $message_condition . " " . $message_type, 'ticket_number' => $number]);
                $notification        = new NotificationController();
                $notification->model = $this->ticket;
                if ($to) {
                    $notification->createNotification($message, $to, null, [$to]);
                }
            }
        } catch (\Exception $e) {
//            dd($e);
        }
    }
}
