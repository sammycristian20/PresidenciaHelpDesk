<?php


namespace database\seeds\v_3_4_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Theme\Widgets;
use App\Model\Common\Template;
use App\Model\helpdesk\Settings\CommonSettings;

class DatabaseSeeder extends Seeder
{
    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        $this->updateDepartmentBusinessHourSeeder();
        $this->updateWidgetsType();
        $this->removeGarbageTemplates();
        $this->toggleOrgShowTicketSettings();
    }

    private function updateDepartmentBusinessHourSeeder()
    {
        $businessHoursIds = BusinessHours::pluck('id')->toArray();
        if($businessHoursIds){
            Department::whereNotIn('business_hour', $businessHoursIds)->update(['business_hour' => 0]);
        }
    }
    
    private function updateWidgetsType()
    {
        Widgets::where('name','LIKE', 'footer%')->update([
            'type' => 'footer'
        ]);

    }

    /**
     * In few installations some templates with category as common-tmeplates
     * This method removes those templates.
     */
    private function removeGarbageTemplates()
    {

        Template::where('template_category', 'common-tmeplates')->delete();
    }

    private function toggleOrgShowTicketSettings()
    {
        CommonSettings::where('option_name', 'user_show_org_ticket')->update([
            'status' => !(bool)commonsettings('user_show_org_ticket','','status')
        ]);
    }
}

