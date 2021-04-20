<?php

namespace App\Model\helpdesk\Email;

use App\BaseModel;
use App\Model\helpdesk\Ticket\Ticket_Priority as TicketPriority;
use App\Model\helpdesk\Settings\System;
use App\Model\helpdesk\Settings\Ticket as SettingsTicket;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;
use Crypt;

class Emails extends BaseModel
{

    protected $table    = 'emails';
    protected $fillable = [
        'email_address', 'email_name', 'department', 'priority', 'help_topic',
        'user_name', 'password', 'fetching_host', 'fetching_port', 'fetching_protocol', 'fetching_encryption', 'mailbox_protocol',
        'folder', 'sending_host', 'sending_port', 'sending_protocol', 'sending_encryption', 'internal_notes', 'auto_response',
        'fetching_status', 'move_to_folder', 'delete_email', 'do_nothing',
        'sending_status', 'authentication', 'header_spoofing', 'imap_config',

        //credential variables for external services
        'block_auto_generated','key','region','domain','secret','version',

        /**
         * tells if the particular mail is available for fetch or not
         */
        'available_for_fetch',

        /**
         * timestamp of the last time when mail was fetched
         */
        'last_fetched_at'
    ];

    public function getCurrentDrive()
    {
        $drive        = $this->attributes['sending_protocol'];
        $mailServices = new \App\Model\MailJob\MailService();
        $id           = "";
        $mailService  = $mailServices->where('short_name', $drive)->first();
        if ($mailService)
        {
            $id = $mailService->id;
        }
        return $id;
    }

    public function getExtraField($key)
    {
        $value    = "";
        $id       = $this->attributes['id'];
        $services = new \App\Model\MailJob\FaveoMail();
        $service  = $services->where('email_id', $id)->where('key', $key)->first();
        if ($service)
        {
            $value = $service->value;
        }
        return $value;
    }

    public function extraFieldRelation()
    {
        $related = "App\Model\MailJob\FaveoMail";
        return $this->hasMany($related, 'email_id');
    }

    public function deleteExtraFields()
    {
        $fields = $this->extraFieldRelation()->get();
        if ($fields->count() > 0)
        {
            foreach ($fields as $field)
            {
                $field->delete();
            }
        }
    }

    /**
     * decrypts the password while fetching
     */
    public function getPasswordAttribute($value)
    {
        if ($value)
        {
            try
            {
                return Crypt::decrypt($value);
            }
            catch (\Illuminate\Contracts\Encryption\DecryptException $e)
            {
                return $value;
            }
        }
        return $value;
    }
    /**
     * encrypts the password while storing
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value ? Crypt::encrypt($value) : $value;
    }

    public function delete()
    {
        $this->deleteExtraFields();
        $this->removeDepartmentOutgoingdDependency();
        parent::delete();
    }

    /**
     *@category function to get releation of emails in department
     *@param void
     *@return 
     */
    public function departmentRelation()
    {
        $related = 'App\Model\helpdesk\Agent\Department';
        return $this->hasMany($related, 'outgoing_email');
    }

    /**
     *@category function to remove ortgoing refrerence of emails from department table
     *@param
     *@return
     */
    public function removeDepartmentOutgoingdDependency()
    {
        $depts = $this->departmentRelation()->get();
        if(count($depts) > 0) {
            foreach ($depts as $dept) {
                $dept->update([
                    'outgoing_email' => ''
                ]);
            }
        }
    }
    
    /**
     * department relation with emails
     */
    public function department()
    {
        return $this->hasOne('App\Model\helpdesk\Agent\Department', 'id','department')->select('id','name');
    }
    
    /**
     * priority relation with emails
     */
    public function priority()
    {
        return $this->hasOne('App\Model\helpdesk\Ticket\Ticket_Priority', 'priority_id','priority')->select('priority_id','priority as name');
    }
    
    /**
     * helptopic relation with emails
     */
    public function helptopic()
    {
        return $this->hasOne('App\Model\helpdesk\Manage\Help_topic','id','help_topic')->select('id','topic as name');
    }
    
    
    /**
     * gets default priority if $this->priority is null
     */
    public function getPriorityAttribute($priority){

        $isActive = TicketPriority::where('priority_id',$priority)->where('status',1)->first();
        
        //if not active or not found, we will return default priority
        if(!$isActive){
            return TicketPriority::where('is_default',1)->first()->priority_id;
        }              

        return $priority;
    }
    
    /**
     * gets default department if $this->department is null
     */
    public function getDepartmentAttribute($department){
        
        // if department is empty, give department as selected helptopic
        if(!$department){

            //check for helptopic
            if($this->help_topic){
                //take department fron helptopic table
                return HelpTopic::find($this->help_topic)->department;
            }

            //if helptopic is not found
            $system = System::find(1);
            return $system->department;
        }
        return $department;
    }
    
    /**
     * gets default help topic if $this->help_topic is null
     */
    public function getHelpTopicAttribute($helpTopic){
        //if helptopic is empty, selected helptopic is whatever department is corresponding to
        //if helptopic is found and it is inactive, default helptopic should be given

        $isActive = HelpTopic::where('id',$helpTopic)->where('status',1)->count();
        
        //if not active or not found, we will return default helptopic
        if(!$isActive){
            $settingsTicket = SettingsTicket::find(1);
            return $settingsTicket->help_topic;
        }     

        return $helpTopic;
    }

    /**
     * is_default column appended in model
     */
    protected $appends = ['is_default'];
    
    /**
     * gets is_default value for emails(If the email is default or not for system)
     * return bool
     */
    public function getIsDefaultAttribute()
    {   
        $defaultEmail = null;
        $defaultSystemEmail = \App\Model\helpdesk\Settings\Email::where('id', '=', '1')->first();
        if($defaultSystemEmail->sys_email) {
            $defaultEmail = $defaultSystemEmail->sys_email;
        } 
        return $this->attributes['id'] == $defaultEmail;
    }
}
