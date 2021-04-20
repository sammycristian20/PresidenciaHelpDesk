<?php

namespace App\FaveoLog\database\seeds;

use database\seeds\DatabaseSeeder as Seeder;
use App\FaveoLog\Model\LogCategory;
use App\Model\MailJob\Condition;

class LogSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedLogCategories();
        $this->seedConditionTableForDeleteLogCron();
    }

    private function seedLogCategories()
    {
        LogCategory::updateOrCreate(['name'=>'ticket-create']);
        LogCategory::updateOrCreate(['name'=>'ticket-reply']);
        LogCategory::updateOrCreate(['name'=>'ticket-update']);
        LogCategory::updateOrCreate(['name'=>'ticket-escalate']);

        LogCategory::updateOrCreate(['name'=>'user-create']);
        LogCategory::updateOrCreate(['name'=>'user-update']);

        LogCategory::updateOrCreate(['name'=>'mail-fetch']);

        LogCategory::updateOrCreate(['name'=>'report']);
        LogCategory::updateOrCreate(['name'=>'rating']);
        LogCategory::updateOrCreate(['name'=>'default']);
    }

    private function seedConditionTableForDeleteLogCron()
    {
        Condition::updateOrCreate(['job'=>'logs','value'=>'daily','icon'=>'glyphicon glyphicon-trash','command'=>'logs:delete','job_info'=>'logs-delete-info']);
    }

}
