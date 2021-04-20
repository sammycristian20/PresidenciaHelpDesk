<?php

namespace database\seeds\v_2_1_7;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;

class DatabaseSeeder extends Seeder
{

    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        $this->updateApprovalWorkflowType();
    }

    /**
     * method to update approval workflow type to 'approval_workflow'
     * @return void
     */ 
    private function updateApprovalWorkflowType()
    {
        ApprovalWorkflow::where('type', '')->update(['type' => 'approval_workflow']);
    }
}
