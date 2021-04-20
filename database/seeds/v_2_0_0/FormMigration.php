<?php

namespace database\seeds\v_2_0_0;

use database\seeds\DatabaseSeeder as Seeder;
use DB;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Manage\Help_topic as Helptopic;
use App\Model\helpdesk\Agent\Department;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Model\helpdesk\Ticket\Ticket_Form_Data as TicketFormData;
use App\UserAdditionalInfo;
use App\Model\helpdesk\Agent_panel\ExtraOrg;

class FormMigration extends BaseFormMigration
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->ticketFormMigration();

        $this->userFormMigration();

        $this->organisationFormMigration();

        $this->formDataMigration();

        $this->dropDepartmentOldNodesColumn();

        $this->dropHelptopicOldNodesColumn();
    }

    /**
     * Handles ticket form migration from old to new db structure
     * @return null
     */
    private function ticketFormMigration()
    {
      $ticketFormFields = json_decode(DB::table('forms')->where('form', 'ticket')->value('json'));

      // initially forms are stored in invalid JSON format in the DB, but after updating an form
      // field, it makes it valid JSON (REASON: only god knows)
      // So, intially we don't need to migrate
      if(!$ticketFormFields){
        return;
      }

      foreach ($ticketFormFields as $form) {
        $formCategory = FormCategory::where('category', 'ticket')->first();

        if($form->default == 'yes'){
          $this->handleDefaultField($formCategory, $form, $form->title);
        }

        if($form->default == 'no'){
          $parentQuery = $formCategory->formFields();
          $this->handleCustomField($parentQuery, $form);
        }
      }

      $this->handleHelptopicChildFields();

      $this->handleDepartmentChildFields();
    }

    /**
     * Handles user form migration from old to new db structure
     * @return null
     */
    private function userFormMigration()
    {
      // user form field
      $userFormFields = json_decode(DB::table('forms')->where('form', 'user')->value('json'));

      // initially forms are stored in invalid JSON format in the DB, but after updating an form
      // field, it makes it valid JSON (REASON: only god knows)
      // So, intially we don't need to migrate
      if(!$userFormFields){
        return;
      }

      // for default fields just check if display is correct
      foreach ($userFormFields as $form) {

        $formCategory = FormCategory::where('category', 'user')->first();

        // if title contains Organization, replace it with Organisation
        $title = str_replace('Organization', 'Organisation', $form->title);

        // for organisation department migration
        if($title == 'Department Name'){
          $title = 'Organisation Department';
        }

        // Address is stored as a custom field in current DB because of some
        // Meta-thermodynamic-quasi-quantum-cosmological reason which is beyond
        // a normal human's undestanding.
        if($form->default == 'yes' || $form->title == 'Address'){
          $this->handleDefaultField($formCategory, $form, $title);
        }

        if($form->default == 'no' && $form->title != 'Address'){
          $parentQuery = $formCategory->formFields();
          $this->handleCustomField($parentQuery, $form);
        }
      }
    }

    /**
     * Handles organisation form migration from old to new db structure
     * @return null
     */
    private function organisationFormMigration()
    {
       $organisationFormFields = json_decode(DB::table('forms')->where('form', 'organisation')->value('json'));

       // initially forms are stored in invalid JSON format in the DB, but after updating an form
       // field, it makes it valid JSON (REASON: only god knows)
       // So, intially we don't need to migrate
       if(!$organisationFormFields){
         return;
       }

       // for default fields just check if display is correct
       foreach ($organisationFormFields as $form) {

         $formCategory = FormCategory::where('category', 'organisation')->first();

         // if title contains Organization, replace it with Organisation
         $title = str_replace('Organization', 'Organisation', $form->title);

         // for organisation department migration
         if($title == 'Department'){
           $title = 'Organisation Department';
         }

         if($form->default == 'yes'){
           $this->handleDefaultField($formCategory, $form, $title);
         }

         if($form->default == 'no'){
           $parentQuery = $formCategory->formFields();
           $this->handleCustomField($parentQuery, $form);
         }
       }
    }

    /**
     * handles default fields updation/creation
     * @param FormCategory $formCategory
     * @param Object $form
     * @param String $title title of the field
     * @return
     */
    private function handleDefaultField(FormCategory $formCategory, $form, string $title)
    {
      // default field handling
      $formField = $formCategory->formFields()->where('title', $title)->first();

      if($formField){
        $this->updateFieldVisibilty($formField, $form);
        $this->handleLabels($form, $formField);
      }
    }

    /**
     * Handles helptopic child fields
     * @return
     */
    private function handleHelptopicChildFields()
    {
      // query for all helptopics, gets its form and create custom fields accordingly
      $helptopics = Helptopic::select('id', 'nodes')->get();
      foreach ($helptopics as $helptopic) {
        $parentQuery = $helptopic->nodes();
        $nodes = $helptopic->nodes;
        if($nodes){
          $nodes = json_decode($nodes);
          foreach ($nodes as $node) {
            $this->handleCustomField($parentQuery, $node);
          }
        }
      }
    }

    /**
     * Handles department child fields
     * @return
     */
    private function handleDepartmentChildFields()
    {
      // query for all departments, gets its form and create custom fields accordingly
      $departments = Department::select('id', 'nodes')->get();
      foreach ($departments as $department) {
        $parentQuery = $department->nodes();
        $nodes = $department->nodes;
        if($nodes){
          $nodes = json_decode($nodes);
          foreach ($nodes as $node) {
            $this->handleCustomField($parentQuery, $node);
          }
        }
      }
    }

    /**
     * handles Custom form creation
     * @param QueryBuilder $formCategory
     * @param Object $form
     * @return null
     */
    private function handleCustomField($parentQuery, $form)
    {
      return $this->updateFormFields($form, $parentQuery);
    }

    /**
     * Migrates form data
     * @return null
     */
    private function formDataMigration()
    {
      $this->ticketDataMigration();

      $this->userDataMigration();

      $this->organisationDataMigration();
    }

    /**
     * Migrates ticket data from old db structure to new
     * @return null
     */
    private function ticketDataMigration()
    {
      // ticket data migration
      $formData = TicketFormData::get();
      foreach ($formData as $data) {
        $this->updateInCustomFormValueTable($data->key, $data->content, $data->ticket_id, 'App\Model\helpdesk\Ticket\Tickets');
      }
    }

    /**
     * Migrates user data from old db structure to new
     * @return null
     */
    private function userDataMigration()
    {
      $formData = UserAdditionalInfo::get();

      foreach ($formData as $data) {
        $this->updateInCustomFormValueTable($data->key, $data->value, $data->owner, 'App\User');
      }
    }

    /**
     * Migrates organisation data from old db structure to new
     * @return null
     */
    private function organisationDataMigration()
    {
      $formData = ExtraOrg::get();

      foreach ($formData as $data) {
        $this->updateInCustomFormValueTable($data->key, $data->value, $data->org_id, 'App\Model\helpdesk\Agent_panel\Organization');
      }
    }

    /**
     * Removes old nodes column since nodes is a relation from now instead of a column
     * NOTE: moving this from migration to seeder so that it can run along with the seeder instead of running in advance
     */
    private function dropHelptopicOldNodesColumn()
    {
        $this->dropColumnIfExist('help_topic', 'nodes');
    }

    /**
     * Removes old nodes column since nodes is a relation from now instead of a column
     * NOTE: moving this from migration to seeder so that it can run along with the seeder instead of running in advance
     */
    private function dropDepartmentOldNodesColumn()
    {
        $this->dropColumnIfExist('department', 'nodes');
    }

    /**
     * Function checks given table has given column then drops the column
     *
     * @param String $tableName  Name of table
     * @param String $columnName Name of Column to drop
     *
     * @return void
     */
    private function dropColumnIfExist(string $tableName, string $columnName):void
    {
        if (Schema::hasColumn($tableName, $columnName))
        {
            Schema::table($tableName, function($table) use($columnName) {
                $table->dropColumn([$columnName]);
            });
        }
    }
}
