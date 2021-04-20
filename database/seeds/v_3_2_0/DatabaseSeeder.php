<?php

namespace database\seeds\v_3_2_0;

use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\TicketRecur\Recur;
use App\Model\helpdesk\TicketRecur\RecureContent;
use App\User;
use database\seeds\DatabaseSeeder as Seeder;
use Illuminate\Database\Schema\Blueprint;
use App\Model\helpdesk\Settings\Plugin;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\TicketStatusType;
use Config;
use DB;
use File;
use Schema;
use App\Model\helpdesk\Agent_panel\Organization;


class DatabaseSeeder extends Seeder
{
    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        $this->activatePluginsByDefaultSeeder();
        $this->disableSocialPluginForever();
        $this->addProbePassPhraseInDotEnv();
        $this->reportExportSeeder();
        $this->removeCloseApproval();
        $this->migrateOldRecurToNew();
        $this->organizationLogoSeeder();
    }

    /**
     * method to activate plugins by default
     * @return null
     */
    private function activatePluginsByDefaultSeeder()
    {
        // this seeder needs to be executed in production environment only
        // executing this seeder in development environment, will lead to failure of test cases
        if ((Config::get('app.env') == 'production')) {
            $activatePluginFilePath = storage_path("activate-plugin.txt");
            if (file_exists($activatePluginFilePath)) {
                $pluginNames = explode(',', File::get($activatePluginFilePath));
                $pluginInstance = new Plugin();
                $basePluginDirectory = app_path(). DIRECTORY_SEPARATOR . "Plugins";
                foreach ($pluginNames as $pluginName) {
                    $pluginPath = $basePluginDirectory.DIRECTORY_SEPARATOR.$pluginName;
                    if (file_exists($pluginPath) && is_null($pluginInstance->where('name', $pluginName)->first())) {
                        $pluginInstance->updateOrCreate(['name' => $pluginName, 'path' => $pluginName, 'status' => 1, 'version' => '0.0.0']);
                    }
                }
            }
        }
    }

    private function disableSocialPluginForever()
    {
        $plugin = Plugin::where('name','Social')->first();
        if($plugin) $plugin->delete();
        $dir = app_path() . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . "Social";
        $this->deleteDirectory($dir);
    }

    private function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            chmod($dir . DIRECTORY_SEPARATOR . $item, 0777);
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        chmod($dir, 0777);

        return rmdir($dir);
    }

    private function addProbePassPhraseInDotEnv()
    {
        $file = base_path().DIRECTORY_SEPARATOR.'.env';
        if(file_exists($file) && (strpos($datacontent = File::get($file), "PROBE_PASS_PHRASE") === false)) {
            /**
             * To ensure PROBE_PASS_PHRASE is added as new variable we append "\n"
             * when .env does not have new line character at the end(useful during updating
             * from older version)
             */
            $datacontent .= ((substr($datacontent, -1) !== "\n") ? "\n": "")."PROBE_PASS_PHRASE=".md5(uniqid())."\r\n";
            File::put($file, $datacontent);
        }
    }

    private function reportExportSeeder()
    {
        // setting option value to 1000, so that if somebody has changed it to something else, it can be restored to 1000,
        // because report export can have side-effects if the number is not a multiple of 1000
        return CommonSettings::where("option_name", "reports_records_per_file")->update(["option_value"=> 1000]);
    }
    
    /**
     * Function to remove close approval and its effects from the database.
     * It moves all tickets from approval(sent using close approval) to open.
     * It also run migration updates and removes unnecessary tables and columns
     * from the database.
     *
     * Assumptions:
     * - In an active system if close approval is being used there must not be more
     *   than 1000 pending for approval.
     * - Old close approval is working fine and when all approvers approve or deny the
     *   close ticket request it sets the status back to intended status.
     * - approval by any single approval closes the ticket
     * - System might have tickets set for approval using new approval so we only
     *   move the tickets which have been enforced by old close approval and does not have
     *   new Approval with PENDING status irrespective of the status old close approval
     */
    public function removeCloseApproval()
    {
        $ticketsHavingApprovalStatus = Tickets::whereIn('status',getStatusArray('approval'))->get();
        foreach ($ticketsHavingApprovalStatus as $ticket) {
            $pending = DB::table('approval_metas')->where([['ticket_id', '=', $ticket->id],['option', '=', 'approver']])->count();
            /**
             * $pending should not be 0 and $ticket should not have "PENDING" approvals
             */
            if($pending && !(bool)$ticket->approvalStatus()->where('status', 'PENDING')->count()) {
                $ticket->status = \App\Helper\Finder::defaultStatus(1)->id;
                $ticket->save();
            }
        }
        $this->handleMigrations();
    }

    /**
     * Function to handle migration update in the schema for removing
     * close approval. Reason for handling migration in the seeder is to
     * ensure if any system has tickets in approval using old close approval
     * they are moved to open and then database is cleaned.
     */
    private function handleMigrations()
    {
        $this->cleanTicketsTable();
        $this->cleanApprovalTables();
        $this->cleanStatusTables();
    }

    /**
     * Function to remove unnecessary old columns from tickets table
     */
    private function cleanTicketsTable():void
    {
        $this->dropColumn('tickets', 'approval');
        $this->dropColumn('tickets', 'follow_up');
    }

    /**
     * Function drops table from schema if exists
     * Deletes table related to close approval and followup
     */
    private function cleanApprovalTables():void
    {
        Schema::dropIfExists('approval');
        Schema::dropIfExists('approval_metas');
        Schema::dropIfExists('followup');
    }

    /**
     * Function to drop columns from schema
     * 
     * @param   string  $table  name of the table from which column needs to be dropped
     * @param   string  $column name of the column to be dropped
     * @return  void 
     */
    private function dropColumn(string $table, string $colunm):void
    {
        if (Schema::hasColumn($table, $colunm)){
            Schema::table($table, function (Blueprint $t) use($colunm) {
                $t->dropColumn($colunm);
            });
        }
    }

    /**
     * Function removes
     * - 'approval' from ticket_status_type table
     * - 'Request for close' and other statuses with 'approval' as their purpose
     *    from ticket_status table 
     *
     * Note: We are not removing these from DatabaseSeeder which initially seeds the
     *       statuses to ensure consistency in the database of old and new installtions
     */
    private function cleanStatusTables()
    {
        $statusWithApprovalAsPurpose = getStatusArray('approval');
        Ticket_Status::whereIn('id', $statusWithApprovalAsPurpose)->delete();
        TicketStatusType::where('name', 'approval')->delete();
    }

    private function migrateOldRecurToNew()
    {
        $recurMetas = RecureContent::where("option", "requester")->get();
        foreach ($recurMetas as $element){
            // get that user by its username or email
            $userId = User::where("user_name", $element->value)->orWhere("email", $element->value)->value("id");
            if($userId){
                $element->value = $userId;
                $element->save();
            }
        }
    }

    /*
    * This method moves organization logo image file from private folder to public 
      folder 
    */

    private function organizationLogoSeeder()
    {
        $organizations = Organization::where('logo', '!=', "")->select('id', 'logo')->get();
        foreach ($organizations as $organization) {


            if (strpos($organization->logo, 'uploads') == false) {
                $start = strpos($organization->logo, '.');
                $length = strlen($organization->logo);
                $imageFileName = substr($organization->logo, $start + 1, $length - 1);

                $fileName = rand(0000, 9999) . '_' . $imageFileName;
                $targetFile = public_path('uploads' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR);
                //checking file exit or not
                if(file_exists($organization->logo)){
                    
                  rename($organization->logo, $targetFile . $fileName);
                }
                
                $absolutePath = DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR;

                Organization::where('id', $organization->id)->update(['logo' => $absolutePath . $fileName]);
            }
        }
    }
}
