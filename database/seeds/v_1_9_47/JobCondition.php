<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\MailJob\Condition;

class JobCondition extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Condition::count() > 0) {
            return $this->runUpdate();
        }

        return $this->runCreate();
    }

    /**
     * Function to create new records in the table on fresh installation
     *
     */
    private function runCreate()
    {
        $data = $this->getDefaultData();
        foreach($data as $job){
            Condition::create($job);
        }
    }

    /**
     * Function to update new column values into the table or create new record
     * if a row is not present in the table
     */
    private function runUpdate()
    {
        $condition = new Condition;
        $availableJobs = $condition->checkALLActiveJob();
        $data = $this->getDefaultData();
        foreach ($data as $key => $value) {
            if (array_key_exists($value['job'], $availableJobs)) {
                unset($value['value']);
            }
            Condition::updateOrCreate(['job' => $value['job']],$value);
        }
        $this->updatePluginCrons($availableJobs, $condition);
    }

    private function getDefaultData()
    {
        $data = [
            [
                "job" => "fetching", "value" => "everyFiveMinutes",
                "icon" => "fa fa-arrow-circle-o-down", "command" => "ticket:fetch",
                "job_info" => "fetching-info"
            ], [
                "job" => "notification", "value" => "daily",
                "icon" => "fa fa-line-chart", "command" => "report:send",
                "job_info" => "notification-info", "active" => 0
            ], [
                "job" => "work", "value" => "yearly",
                "icon" => "fa fa-archive", "command" => "ticket:close",
                "job_info" => "work-info"
            ], [
                "job" => "escalation", "value" => "everyTenMinutes",
                "icon" => "fa fa-hourglass-half", "command" => "send:escalation",
                "job_info" => "escalation-info"
            ], [
                "job" => "recur", "value" => "daily",
                "icon" => "fa  fa-repeat", "command" => "ticket:recur",
                "job_info" => "recur-info"
            ], [
                "job" => "check-updates", "value" => "daily",
                "icon" => "fa fa-refresh", "command" => "faveo:checkupdate",
                "job_info" => "check-updates-info"
            ]
        ];

        return $data;
    }

    /**
     * Function to update details of Custom cron of Plugins. As of now only LDAP plugin
     * has a cutom cron so we are checking the AD sync cron for updating the system
     * in which the cron is already activated
     *
     * @param  Array       $availableJobs  array containing list of active jobs in the system
     * @param  Condition  $condition      instance on Condition model
     */
    private function updatePluginCrons(array $availableJobs, $condition)
    {
        if (array_key_exists('ldap', $availableJobs)) {
            $job = [
                "icon" => "fa fa-cloud-download", "command" => "ldap:sync",
                "job_info" => "ldap-info", "plugin_job" => 1, "plugin_name" => "Ldap"
            ];
            $condition->updateOrCreate(['job' => 'ldap'], $job);
        }
    }
}
