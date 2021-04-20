<?php
namespace App\Upgrade\database\comm_v1_9_6\seeds;
//seeders
use \App\Itil\database\seeds\SdChangePriority;
use \App\Itil\database\seeds\SdChangeStatus;
use \App\Itil\database\seeds\SdImpactSeeder;
use \App\Itil\database\seeds\SdChangeType;
use \App\Itil\database\seeds\SdReleasePriority;
use \App\Itil\database\seeds\SdReleaseStatus;
use \App\Itil\database\seeds\SdReleaseType;
use \App\Itil\database\seeds\SdAssetAttachmentTypes;
use \App\Itil\database\seeds\SdLocationCategorySeeder;
/* ------------------------------*/
use AlertSeeder;
use CommonSettingsSeeder;
use CustomFormSeeder;
use PortalSeeder;
use SlaSeeder;
use TickettypeSeeder;
use TemplateSeeder;
use TicketStatusSeeder;
// use \App\database\seeds\
// use \App\database\seeds\
// use \App\database\seeds\

//models
use \App\Itil\Models\Problem\Impact;
use App\Model\helpdesk\Settings\Approval;
use App\Model\helpdesk\Settings\Followup;
//class
use database\seeds\DatabaseSeeder as Seeder;


class CommToProSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $seed1 = new SdImpactSeeder();
        $seed1->run();

        $seed2 = new SdLocationCategorySeeder();
        $seed2->run();
        
        $seed3 = new SdAssetAttachmentTypes();
        $seed3->run();
        
        $seed4 = new SdChangePriority();
        $seed4->run();
        
        $seed5 = new SdChangeType();
        $seed5->run();
        
        $seed6 = new SdChangeStatus();
        $seed6->run();

        $seed7 = new SdReleasePriority();
        $seed7->run();
        
        $seed8 = new SdReleaseStatus();
        $seed8->run();
        
        $seed9 = new SdReleaseType();
        $seed9->run();

        $seed10 = new AlertSeeder();
        $seed10->run();

        $seed11 = new CommonSettingsSeeder();
        $seed11->run();

        $seed12 = new CustomFormSeeder();
        $seed12->run();

        $seed13 = new PortalSeeder();
        $seed13->run();

        $seed14 = new TickettypeSeeder();
        $seed14->run();

        $seed15 = new SlaSeeder();
        $seed15->run();

        $seed16 = new TemplateSeeder();
        $seed16->run();

        $seed17 = new TicketStatusSeeder();
        $seed17->run();
        
        $this->approvalSeeder();
        $this->followUpSeeder();
    }


    private function approvalSeeder()
    {
        Approval::create(['name' => 'approval', 'status' => '0']);
    }

    private function followUpSeeder()
    {
        Followup::create(['name' => 'followup']);
    }

}

