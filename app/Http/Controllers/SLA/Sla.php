<?php

namespace App\Http\Controllers\SLA;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Model\helpdesk\Manage\Sla\SlaTargets;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Manage\Sla\BusinessHours;

class Sla extends Controller {

    public $id;
    public $sla;
    protected $name;
    protected $admin_note;
    protected $status;
    protected $sla_target;
    protected $departments;
    protected $companies;
    protected $ticket_type;
    protected $ticket_source;
    protected $today;
    public $priority;
    public $business_hour;

    public function __construct($slaid) {
        $plan = new Sla_plan();
        $sla = $plan->find($slaid);
        $this->sla = $sla;
        if ($sla) {
            $this->name = $sla->name;
            $this->admin_note = $sla->admin_note;
            $this->status = $sla->status;
            $this->id = $sla->id;
            //$this->today = date('l', time()+(86400*0));
            $this->today = date('l', time());
            $this->priority = $this->getPrority()->priority_id;
            $this->business_hour = $this->sla->target->businessHour;
        }
    }
    /**
     * get the id of the sla
     * @return integer
     */
    public function getId() {
        if ($this->sla) {
            return $this->sla->id;
        }
    }
    /**
     * get the name of the sla 
     * @return string
     */
    public function getName() {
        if ($this->sla) {
            return $this->sla->name;
        }
    }
    /**
     * get the note of the sla
     * @return string
     */
    public function getNote() {
        if ($this->sla) {
            return $this->sla->admin_note;
        }
    }
    /**
     * get the status of the sla
     * @return boolean
     */
    public function getStatus() {
        $check = false;
        if ($this->sla && $this->sla->status == 1) {
            $check = true;
        }
        return $check;
    }
    /**
     * get the target of the model
     * @return mixed
     */
    protected function getTargetModel() {
        if ($this->sla) {
            $targetid = $this->sla->sla_target;
            if ($targetid) {
                $model = SlaTargets::find($targetid);
                return $model;
            }
        }
    }
    /**
     * get priority of the sla
     * @return Ticket_Priority
     */
    protected function getPrority() {
        if ($this->sla) {
            $target = $this->getTargetModel();
            if ($target) {
                $priorityid = $target->priority_id;
                $model = Ticket_Priority::find($priorityid);
                return $model;
            }
        }
    }
    /**
     * get the priority name
     * @return string
     */
    public function getProrityName() {
        if ($this->sla) {
            $priority = $this->getPrority();
            if ($priority) {
                return $priority->priority;
            }
        }
    }
    /**
     * get the priority id of the ticket
     * @return integer
     */
    public function getProrityId() {
        if ($this->sla) {
            $priority = $this->getPrority();
            if ($priority) {
                return $priority->id;
            }
        }
    }
    /**
     * emailing escalation enabled yes/no
     * @return boolean
     */
    public function isSendEmail() {
        if ($this->sla) {
            $target = $this->getTargetModel();
            $check = false;
            if ($target && $target->send_email == 1) {
                $check = true;
            }
            return $check;
        }
    }
    /**
     * sms escalation enabled yes/no
     * @return boolean
     */
    public function isSendSms() {
        if ($this->sla) {
            $target = $this->getTargetModel();
            $check = false;
            if ($target && $target->send_sms == 1) {
                $check = true;
            }
            return $check;
        }
    }
    /**
     * get the response time of the sla
     * @return mixed
     */
    public function respondTime() {
        if ($this->sla) {
            $target = $this->getTargetModel();
            if ($target) {
                $respond = $target->respond_within;
                $array = explode('-', $respond);
                $time = checkArray(0, $array);
                $period = checkArray(1, $array);
                return $this->timeInMinutes($time, $period);
            }
        }
    }
    /**
     * get the resolve time of the sla
     * @return mixed
     */
    public function resolveTime() {
        if ($this->sla) {
            $target = $this->getTargetModel();
            if ($target) {
                $respond = $target->resolve_within;
                $array = explode('-', $respond);
                $time = checkArray(0, $array);
                $period = checkArray(1, $array);
                return $this->timeInMinutes($time, $period);
            }
        }
    }
    /**
     * cinvert to minutes
     * @param integer $time
     * @param string $period
     * @return int
     */
    public function timeInMinutes($time, $period) {
        switch ($period) {
            case "min":
                return $time;
            case "hrs":
                return $time * 60;
            case "days":
                return $time * 24 * 60;
            case "months":
                return $time * 24 * 30 * 60;
            default :
                return 0;
        }
    }
    /**
     * get the departments forced to the sla
     * @return array
     */
    public function getDepartments() {
        if ($this->sla) {
            return explode(',', $this->sla->apply_sla_depertment);
        }
    }
    /**
     * get the company forced to the sla
     * @return array
     */
    public function getCompanies() {
        if ($this->sla) {
            return explode(',', $this->sla->apply_sla_company);
        }
    }
    /**
     * get the types forced to the sla
     * @return array
     */
    public function getTicketTypes() {
        if ($this->sla) {
            return explode(',', $this->sla->apply_sla_tickettype);
        }
    }
    /**
     * get the source forced to the sla
     * @return array
     */
    public function getTicketSources() {
        if ($this->sla) {
            return explode(',', $this->sla->apply_sla_ticketsource);
        }
    }
    /**
     * get the approaches
     * @return mixed
     */
    protected function getApproaches() {
        if ($this->sla) {
            return $this->sla->approach;
        }
    }
    /**
     * get the response time
     * @param string $time
     * @return mixed
     */
    public function getApproachResponseTime($time) {
        if ($this->sla) {
            $approach = $this->getApproaches();
            if ($approach) {
                $response_escalate_time = $approach->response_escalate_time;
                return $time->subMinutes($response_escalate_time);
            }
        }
    }
    /**
     * approach response persons
     * @return array
     */
    public function getApproachResponsePerson() {
        if ($this->sla) {
            $approach = $this->getApproaches();
            if ($approach) {
                $response_escalate_person = $approach->response_escalate_person;
                return explode(',', $response_escalate_person);
            }
        }
    }
    /**
     * get approach resolution time
     * @param string $time
     * @return mixed
     */
    public function getApproachResolutionTime($time) {
        if ($this->sla) {
            $approach = $this->getApproaches();
            if ($approach) {
                $response_resolution_time = $approach->resolution_escalate_time;
                return $time->subMinutes($response_resolution_time);
            }
        }
    }
    /**
     * get approach resolution persons
     * @return array
     */
    public function getApproachResolutionPerson() {
        if ($this->sla) {
            $approach = $this->getApproaches();
            if ($approach) {
                $response_resolution_person = $approach->resolution_escalate_person;
                return explode(',', $response_resolution_person);
            }
        }
    }
    /**
     * get violates
     * @return mixed
     */
    protected function getViolates() {
        if ($this->sla) {
            return $this->sla->violated;
        }
    }
    /**
     * get violate response time
     * @param string $time
     * @return mixed
     */
    public function getViolateResponseTime($time) {
        if ($this->sla) {
            $approach = $this->getViolates();
            if ($approach) {
                $response_escalate_time = $approach->response_escalate_time;
                return $time->addMinutes($response_escalate_time);
            }
        }
    }
    /**
     * get violate response persons
     * @return array
     */
    public function getViolateResponsePerson() {
        if ($this->sla) {
            $approach = $this->getViolates();
            if ($approach) {
                $response_escalate_person = $approach->response_escalate_person;
                return explode(',', $response_escalate_person);
            }
        }
    }
    /**
     * get violate resolution time
     * @param string $time
     * @return mixed
     */
    public function getViolateResolutionTime($time) {
        if ($this->sla) {
            $approach = $this->getViolates();
            if ($approach) {
                $response_resolution_time = $approach->resolution_escalate_time;
                return $time->addMinutes($response_resolution_time);
            }
        }
    }
    /**
     * get violate resolution person
     * @return array
     */
    public function getViolateResolutionPerson() {
        if ($this->sla) {
            $approach = $this->getViolates();
            if ($approach) {
                $response_resolution_person = $approach->resolution_escalate_person;
                return explode(',', $response_resolution_person);
            }
        }
    }
    /**
     * get business hours
     * @return mixed
     */
    protected function getBusinessHours() {
        if ($this->sla) {
            $business = $this->business_hour;
            return $business;
        }
    }
    /**
     * get business hour name
     * @return string
     */
    public function getBusinessHourName() {
        if ($this->sla) {
            $model = $this->getBusinessHours();
            if ($model) {
                return $model->name;
            }
        }
    }
    /**
     * get business hour description
     * @return string
     */
    public function getBusinessHourDescription() {
        if ($this->sla) {
            $model = $this->getBusinessHours();
            if ($model) {
                return $model->description;
            }
        }
    }
    /**
     * get business hour status
     * @return boolean
     */
    public function getBusinessHourStatus() {
        if ($this->sla) {
            $model = $this->getBusinessHours();
            $check = false;
            if ($model && $model->status) {
                $check = true;
            }
            return $check;
        }
    }
    /**
     * get business hour timezone 
     * @return mixed
     */
    public function getBusinessHourTimezone() {
        if ($this->sla) {
            $model = $this->getBusinessHours();
            if ($model) {
                $time_zone = $model->timezone;
            }
            if (!$time_zone || $time_zone == "0" || $time_zone == "") {
                $systems = new \App\Model\helpdesk\Settings\System();
                $system = $systems->first();
                if ($system) {
                    $time_zone = $system->timezone->name;
                }
            }
            return $time_zone;
        }
    }
    /**
     * get date
     * @param string $format
     * @param string $string
     * @return string
     */
    public function date($format = "m-d", $string = "now") { // "+1 day"
        return gmdate($format, strtotime($string));
    }
    /**
     * get business schedule
     * @param string $string
     * @return boolean
     */
    public function getBusinessSchedule($string = "now") {
        if ($this->sla) {
            $date = $this->date("m-d", $string);
            $day = $this->date('l', $string);
            if ($this->sla) {
                $holiday = $this->isHoliday($date);
                if ($holiday == true) {
                    return false;
                }
                $business = $this->business_hour->schedule()->where('days', $day)->select('id', 'status')->first();
                if ($business) {
                    return $this->getScheduledTime($business);
                }
            }
            return true;
        }
    }
    /**
     * get scheduled time 
     * @param string $schedule
     * @return boolean
     */
    public function getScheduledTime($schedule) {
        if ($schedule) {
            switch ($schedule->status) {
                case "Open_custom":
                    //dd($schedule->custom()->select('open_time', 'close_time')->get()->toArray());
                    return $schedule->custom()->select('open_time', 'close_time')->get()->toArray();
                case "Open_fixed":
                    return true;
                case "Closed":
                    return false;
            }
        }
    }
    /**
     * get hilidat list of the busness hour
     * @return array
     */
    public function getHolidayList() {
        if ($this->sla) {
            return $business = $this->business_hour->holiday()->pluck('name', 'date')->toArray();
        }
    }
    /**
     * check a date is holiday
     * @param string $date
     * @return boolean
     */
    public function isHoliday($date) {
        //dd($date);
        if ($this->sla) {
            $holiday = $this->business_hour->holiday()->where('date', $date)->first();
            if ($holiday) {
                return true;
            }
        }
    }
    /**
     * get the timezone of the business hour
     * @return string
     */
    public function timezone(){
        $time_zone = "";
        if ($this->sla) {
            $model = $this->getBusinessHours();
            if ($model && $model->timezone) {
                $time_zone = $model->timezone;
            }
            if($time_zone==""){
                $time_zone = timezone();
            }
            return $time_zone;
        }
    }
    
    /**
     * in app escalation enabled yes/no
     * @return boolean
     */
    public function isPush() {
        if ($this->sla) {
            $target = $this->getTargetModel();
            $check = false;
            if ($target && $target->in_app == 1) {
                $check = true;
            }
            return $check;
        }
    }

}
