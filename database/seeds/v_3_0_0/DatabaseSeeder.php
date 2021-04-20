<?php

namespace database\seeds\v_3_0_0;

use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Model\helpdesk\Manage\Sla\SlaApproachEscalate;
use App\Model\helpdesk\Manage\Sla\SlaViolatedEscalate;
use App\Model\helpdesk\Manage\UserType;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\TicketSla;
use App\Model\MailJob\Condition;
use Carbon\Carbon;
use database\seeds\DatabaseSeeder as Seeder;
use DB;
use App\Model\helpdesk\Settings\Plugin;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\Common\TemplateSet;
use App\Model\Common\TemplateType;
use App\Model\Common\Template;
use App\Model\helpdesk\Manage\Sla\BusinessHoliday;
use App\Model\helpdesk\Utility\Timezones;
use App\Model\helpdesk\Email\Emails;
use Artisan;
use File;
use Crypt;
use Illuminate\Support\Str;
use Illuminate\Encryption\Encrypter;


class DatabaseSeeder extends Seeder
{
    /**
     * @var array
     */
    private $emails = [];

    /**
     * @var array
     */
    private $ldapUsers = [];

    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        $this->remindersTableCleanup();
        $this->userTypeSeeder();
        $this->defaultSlaSeeder();
        $this->updateExistingTicketsToDefaultSLA();
        $this->updateTicketRemindersCronTime();
        $this->removePlugins();
        $this->addCCTicketViewOptionInCommonSettings();
        $this->addTicketReplyAgentTemplateForClients();
        $this->updateGreenlandTimeZone();
        $this->businessHoursHoliday();
        $this->updateKeys();
    }

    /**
     * Seeds default SLA
     */
    private function defaultSlaSeeder()
    {
        $sla = TicketSla::create(["name"=>"Default", "status"=>1, "order"=>1, "is_default"=>1]);

        // get all priorities
        $priorities = Ticket_Priority::select('priority_id as id', 'priority as name')->get();
        $businessHourId = BusinessHours::where('is_default', 1)->first()->id;

        foreach ($priorities as $priority) {

            $priorityObject = $this->getSlaTimeByPriority($priority->name);

            $sla->slaMeta()->create(["priority_id"=>$priority->id, "business_hour_id" => $businessHourId,
                "respond_within"=> $priorityObject->respond_within, "resolve_within"=>$priorityObject->resolve_within]);
        }
    }

    /**
     * @param string $priority
     * @return object
     */
    private function getSlaTimeByPriority(string $priority)
    {
        switch ($priority){

            case "Normal":
                return (object)['respond_within'=>"diff::4~hour", "resolve_within"=>"diff::9~hour"];

            case "High":
                return (object)['respond_within'=>"diff::2~hour", "resolve_within"=>"diff::4~hour"];

            case "Emergency":
                return (object)['respond_within'=>"diff::1~hour", "resolve_within"=>"diff::2~hour"];

            default:
                return (object)['respond_within'=>"diff::5~hour", "resolve_within"=>"diff::10~hour"];
        }
    }

    private function remindersTableCleanup()
    {
        SlaApproachEscalate::truncate();

        SlaViolatedEscalate::truncate();
    }

    private function userTypeSeeder()
    {
        // adding assignee as another user type
        UserType::create(["key"=>"assignee", "name"=>"Assignee"]);
    }

    private function updateExistingTicketsToDefaultSLA()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        set_time_limit(0);

        // shifting all tickets to default SLA
        DB::table("tickets")->update(["sla"=> 1]);

        // creating threads in all open tickets telling that SLA has been changed
        $openTickets = Tickets::whereIn("status", getStatusArray("open"))->select("id", "sla")->get();


        foreach ($openTickets as $ticket){
            // query the old SLA table to get the name of the SLA
            $oldSlaName = DB::table("sla_plan")->where("id", $ticket->sla)->value("name");

            if($oldSlaName){
                $body = "SLA of this ticket has been changed from <strong>$oldSlaName</strong> to <strong>Default</strong>  SLA during system update. Old SLAs are no longer supported";

                DB::table("ticket_thread")->insert([
                    "thread_type"=> "internal_activity_alert",
                    "ticket_id"=>$ticket->id,
                    "poster"=>"support",
                    "is_internal"=>1,
                    "body"=>$body,
                    "created_at"=> Carbon::now(),
                    "updated_at"=> Carbon::now()
                ]);
            }
        }
    }

    /**
     * Updating CRON time for SLA reminders to 5 minutes
     */
    private function updateTicketRemindersCronTime()
    {
        $condition = Condition::where("job", "escalation")->first();

        if($condition){
            $condition->value = "everyMinute";
            $condition->save();
        }
    }

    /*
    * Temporary removed organization docs and organization Licenses plugin
    *
    */

    private function removePlugins()
    {
        Plugin::whereIn('name',['OrganizationDocs','OrganizationLicenses'])->delete();

    }

    /** Fixes name in timezone table for Greenland
     * There is an issue in timezone table with entry for Greenland
     * it contains Greenland as name which Carbon is unable to recognize.
     * This method updates the name of Greenland to America/Godthab which is
     * one of the actual timezone name of Greenland.
     *
     * @return void
     */
    private function updateGreenlandTimeZone():void
    {
        Timezones::where('name', 'Greenland')->update(['name' => 'America/Godthab']);
    }

    private function addCCTicketViewOptionInCommonSettings()
    {
        CommonSettings::updateOrCreate([
            'option_name' => 'user_show_cc_ticket'
        ],[
            'option_name' => 'user_show_cc_ticket',
            'status'      => 0
        ]);
    }

    private function addTicketReplyAgentTemplateForClients()
    {
        $templateType = TemplateType::where('name', 'ticket-reply-agent')->first();
        $sets = TemplateSet::all()->pluck('id')->toArray();
        foreach ($sets as $set) {
            Template::updateOrCreate(['type' => $templateType->id, 'set_id' => $set, 'template_category' => 'client-templates'],[
                'variable' => '0',
                'name' => 'template-ticket-reply-to-client-by-client',
                'template_category' => 'client-templates',
                'type' => $templateType->id,
                'set_id' => $set,
                'message' => 'Hello {!! $receiver_name !!},<br /><br />'
                    .'{!! $activity_by !!} has made a new reply on the ticket with Id <strong>{!! $ticket_number !!}</strong></.<br /><br />'
                    .'Reply Message</strong><br />'
                    .'{!! $message_content !!}<br /><br />'
                    .'Please follow the link below to check and reply on ticket.<br />'
                    .'{!! $ticket_link !!}<br /><br />'
                    .'Thanks<br />'
                    .'{!! $system_from !!}<br />'
            ]);
        }
    }

    /**
     * This method to add a year for the old customer which has business holiday without year
     */

    private function businessHoursHoliday(){

        $holidayDate = BusinessHoliday::select('id','date')->get()->toArray();
        foreach ($holidayDate as $holiday) {

            if(strlen($holiday['date']) < 6){

                BusinessHoliday::where('id',$holiday['id'])->update(['date' => $holiday['date'].'-'.date("Y")]);
            }
        }
    }

    /**
     * Function adds APP_KEY and JWT_SECRET in .env while updating old system
     * if do not exist.
     *
     * @return void
     */
    private function updateKeys():void
    {
        $file = base_path().DIRECTORY_SEPARATOR.'.env';
        if(file_exists($file)) {
            $datacontent = File::get($file);
            $this->updateAppKey($file, $datacontent);
            $this->updateJWTKey($datacontent);
        }
    }

    /**
     * Function adds APP_KEY and generate new application key if does not
     * exist in .env
     *
     * This method also updates the email passwords after new key generation
     * so that the imap/smtp does not stop working after update
     *
     * This method also updates the LDAP passwords after new key generation
     * so that the LDAP plugin does not stop working after update
     *
     * @param  string $file
     * @param  string $datacontent
     *
     * @return voif
     */
    private function updateAppKey(string $file, string $datacontent) :void
    {
        if(!$this->doesEnvVaribaleExists($datacontent, 'APP_KEY')) {
            $this->fetchAndStoreEmailPassword();
            $this->fetchAndStoreLDAPPassword();
            $datacontent = $datacontent."APP_KEY=base64:h3KjrHeVxyE+j6c8whTAs2YI+7goylGZ/e2vElgXT6I=";
            File::put($file, $datacontent);
            Artisan::call('key:generate', ['--force' => true]);
            // If the key starts with "base64:", we will need to decode the key before handing
            // it off to the encrypter. Keys may be base-64 encoded for presentation and we
            // want to make sure to convert them back to the raw bytes before encrypting.
            if (Str::startsWith($key = $this->key(), 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }
            $encrypter = new Encrypter($key, config('app.cipher'));
            $this->updateEmailsPasswordWithNewKey($encrypter);
            $this->updateLDAPPasswordWithNewKey($encrypter);
        }
    }

    /**
     * Function generates JWT secret key if does not exist in .env
     *
     * @param string $datacontent
     *
     * @return void
     */
    private function updateJWTKey(string $datacontent) :void
    {
        if(!$this->doesEnvVaribaleExists($datacontent, 'JWT_SECRET') && $this->doesEnvVaribaleExists($datacontent, 'DB_INSTALL')) {
            Artisan::call('jwt:secret', ['--force' => true]);
        }
    }

    /**
     * Function checks if given $key exist in $envContent string
     *
     * @param  string $envContent
     * @param  string $key
     *
     * @return bool   true if given key exist otherwise false
     */
    private function doesEnvVaribaleExists(string $envContent, string $key):bool
    {
        return !(strpos($envContent, $key) === false);
    }

    /**
     * Function stores all emails and their passwords as key => value in $emails prop
     *
     * @return void
     */
    private function fetchAndStoreEmailPassword():void
    {
        $this->emails = Emails::all()->pluck('password', 'email_address')->toArray();
    }

    /**
     * Function updates all emails's password
     *
     * @return void
     */
    private function updateEmailsPasswordWithNewKey($encrypter):void
    {
        foreach ($this->emails as $email => $password) {
            Emails::where('email_address', $email)->update([
                'password' => $encrypter->encrypt($password)
            ]);
        }
    }

    /**
     * Function stores all LDAP admin users and their passwords as key => value in $ldapUsers prop
     *
     * @return void
     */
    private function fetchAndStoreLDAPPassword()
    {
        if(class_exists('App\Plugins\Ldap\Model\Ldap') && \Schema::hasTable('ldap')) {
            $this->ldapUsers = \App\Plugins\Ldap\Model\Ldap::all()->pluck('password', 'username')->toArray();
        }
    }

    /**
     * Function updates all LDAP admin's password
     *
     * @return void
     */
    private function updateLDAPPasswordWithNewKey($encrypter)
    {
        foreach ($this->ldapUsers as $username => $password) {
            \App\Plugins\Ldap\Model\Ldap::where('username', $username)->update([
                'password' => $encrypter->encrypt($password)
            ]);
        }
    }


    /**
     * Extract the encryption key from the given configuration.
     *
     * @param  array  $config
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function key()
    {
        return tap(config('app.key'), function ($key) {
            if (empty($key)) {
                throw new RuntimeException(
                    'No application encryption key has been specified.'
                );
            }
        });
    }
}
