<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Manage\Sla\BusinessHours;
use App\Model\helpdesk\Manage\Sla\BusinessHoursSchedules;
use App\Model\helpdesk\Manage\Sla\Sla_plan;
use App\Model\helpdesk\Manage\Sla\SlaTargets;
use App\Model\helpdesk\Ticket\Ticket_Priority;

class SlaSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BusinessHours::truncate();
        BusinessHoursSchedules::truncate();
        SlaTargets::truncate();
        Sla_plan::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $business_hours = BusinessHours::create(['name' => 'Default Business-Hours', 'description' => 'default', 'status' => 1,'is_default'=>1]);

        BusinessHoursSchedules::create(['business_hours_id' => $business_hours->id, 'days' => 'Sunday', 'status' => 'Closed',]);
        BusinessHoursSchedules::create(['business_hours_id' => $business_hours->id, 'days' => 'Monday', 'status' => 'Open_fixed',]);
        BusinessHoursSchedules:: create(['business_hours_id' => $business_hours->id, 'days' => 'Tuesday', 'status' => 'Open_fixed',]);
        BusinessHoursSchedules::create(['business_hours_id' => $business_hours->id, 'days' => 'Wednusday', 'status' => 'Open_fixed',]);
        BusinessHoursSchedules::create(['business_hours_id' => $business_hours->id, 'days' => 'Thursday', 'status' => 'Open_fixed',]);
        BusinessHoursSchedules::create(['business_hours_id' => $business_hours->id, 'days' => 'Friday', 'status' => 'Open_fixed',]);
        BusinessHoursSchedules::create(['business_hours_id' => $business_hours->id, 'days' => 'Saturday', 'status' => 'Closed',]);

        //$priority = Ticket_Priority::first()->id;

        $targets = [
            [
                'name'=>'Low','priority_id'=>1,'sla_id'=>1,
                'respond_within'=>'5-hrs','resolve_within'=>'10-hrs',
                'business_hour_id'=>$business_hours->id,'send_email'=>1,'send_sms'=>0,
            ],
            [
                'name'=>'Normal','priority_id'=>2,'sla_id'=>2,
                'respond_within'=>'4-hrs','resolve_within'=>'9-hrs',
                'business_hour_id'=>$business_hours->id,'send_email'=>1,'send_sms'=>0
            ],
            [
                'name'=>'High','priority_id'=>3,'sla_id'=>3,
                'respond_within'=>'2-hrs','resolve_within'=>'4-hrs',
                'business_hour_id'=>$business_hours->id,'send_email'=>1,'send_sms'=>0
            ],
            [
                'name'=>'Emergency','priority_id'=>4,'sla_id'=>4,
                'respond_within'=>'1-hrs','resolve_within'=>'2-hrs',
                'business_hour_id'=>$business_hours->id,'send_email'=>1,'send_sms'=>0
            ],
        ];
        $n = 0;
        foreach($targets as $target){
            $target = SlaTargets::create($target);
            $this->createSla($target->id, $target['name'],$n);
            $n++;
        }


    }

    public function createSla($targetid,$name,$n){
        $is_default = 0;
        if($n==0){
            $is_default = 1;
        }
        Sla_plan::create([
            'name'=>$name,
            //'admin_note'=>'This is the default sla. Please do not delete this untill create a new one',
            'status'=>1,
            'sla_target'=>$targetid,
            'is_default'=>$is_default,

        ]);
    }

}
