<?php

namespace database\seeds\v_2_0_1;

use database\seeds\DatabaseSeeder as Seeder;
use DB;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Manage\Sla\SlaCustomEnforcements;
use App\Model\Common\TemplateSet;
use App\Model\Common\TemplateType;
use App\Model\Common\Template;
use App\Model\helpdesk\Manage\UserType;
use App\Model\helpdesk\TicketRecur\Recur;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\TicketStatusType;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->slaCustomEnforcementsMigration();
        $this->templateForTicketForwarding();
        $this->seedUserTypes();
        $this->seedRecur();
        $this->statusSeeder();
    }

    /**
     * migrates old custom fields in SLA to new one
     * @return void
     */
    private function slaCustomEnforcementsMigration() : void
    {
      $customSLAEnforcements = SlaCustomEnforcements::select('id','f_name')->get();

      foreach ($customSLAEnforcements as $element) {

        // get data from custom_
        $formFieldId = FormField::where('unique', $element->f_name)->where('default', 0)->value('id');

        if($formFieldId){
          $element->f_name = 'custom_'.$formFieldId;
          $element->save();
        }
      }
    }

    /**
     * Ticket settings seeder
     * @return null
     */
    private function templateForTicketForwarding()
    {
      $templateTypeId = TemplateType::updateOrCreate(['name' => 'ticket-forwarding'])->id;

      Template::updateOrCreate(['type' => $templateTypeId, 'name' => 'ticket-forwarding'],[
          'variable' => '1',
          'template_category' => 'common-tmeplates',
          'name' => 'ticket-forwarding',
          'type' => $templateTypeId,
          'subject' => '',
          'set_id' => '1',
          'message' => 'Hello,<br /><br />'
                  .'Following ticket has been forwarded to you by {!! $user_profile_link !!}<br /><br />'
                  .'{!! $ticket_conversation !!}'
                  .'Thank You.<br />'
                  .'Kind Regards,<br />'
                  .'{!! $system_from !!}'
      ]);
    }

    /**
     * method to seed user types
     * @return null
     */
    private function seedUserTypes()
    {     
        foreach (UserType::get() as $userType) {
            UserType::where('id', $userType->id)->update([
                'key' => $userType->name,
                'name' => implode(' ', array_map('ucfirst', explode('_', $userType->name)))
            ]);
        }

        UserType::updateOrCreate(['key' => 'organization_manager'], [
            'name' => 'Organization Manager',
            'key' => 'organization_manager'
        ]);

        UserType::updateOrCreate(['key' => 'organization_department_manager'], [
            'name' => 'Organization Department Manager',
            'key' => 'organization_department_manager'
        ]);
    }

    private function statusSeeder()
    {

        // has to be added by version-wise
        $statusType = TicketStatusType::updateOrCreate(['name' => 'merged'],['name' => 'merged']);

        Ticket_status::updateOrCreate(['name' => 'Merged', 'purpose_of_status' => $statusType->id],['name' => 'Merged', 'default' => '1', 'visibility_for_client' => '0', 'message' => 'Ticket have been marked as Merged by {!!$user!!}', 'allow_client' => '0', 'visibility_for_agent' => '1', 'purpose_of_status' => $statusType->id, 'secondary_status' => null,
            'send_email' => json_encode(['client'=>'0','admin'=>'1','assigned_agent_team'=>'0']),
            'order' => '7', 'icon' => 'fa fa-clock-o', 'icon_color' => '#f20630', 'halt_sla' => 1]);
    }

    /**
     * method to add recur name to existing recurs without name
     * @return null
     */
    private function seedRecur()
    {
        foreach (Recur::get() as $recur) {
            if ($recur->name) {
                Recur::where('id', $recur->id)->update(['name' => 'Recur' . $recur->id]);
            }
        }
    }
}
