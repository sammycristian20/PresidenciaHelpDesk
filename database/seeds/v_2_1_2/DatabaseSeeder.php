<?php

namespace database\seeds\v_2_1_2;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Settings\Security;
use Cache;

use App\Model\helpdesk\Settings\CommonSettings;
use DB;
use Artisan;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->updateUniqueKeyForFormFields();
        $this->billingMigration();
        $this->passportInstall();


        $this->cdnSettingsSeeder();


        $this->deletePublicMailFetchFolder();


        $this->statusSeeder();


    }

    /**
     * Add mail-send entry to LogCategory
     * @return void
     */
    private function billingMigration()
    {
        $isBillingFormActive = (bool)DB::table('common_settings')
          ->where('optional_field', 'allowWithoutPackage')
          //settings had been updated to store correct logical bool values
          ->where('option_value', 0)->count();

        $formCategory = FormCategory::where('category','ticket')->first();

        // to push this to the bottom of the form
        $sortOrder = $formCategory->formFields()->orderBy('sort_order','DESC')
          ->value('sort_order') + 1;

        // creating form field
        $formField = $formCategory->formFields()
          ->updateOrCreate(['title'=>'Billing'],
            [
            'title'=>'Billing',
            'required_for_agent'=> 0, 'required_for_user'=> 1,
            'display_for_agent'=> 1, 'display_for_user'=> 1,
            'is_edit_visible'=> false,
            'is_active'=> $isBillingFormActive,
            'default'=> 0,
            'sort_order'=> $sortOrder,
            'type'=>'select',
            'api_info'=>'url:=/ticket/form/dependancy?dependency=user-packages;;',
            'unique'=>'package_order',
            'is_deletable'=> false,
            'is_customizable'=> false,
            'is_observable'=>false,
            'is_filterable'=>false,
          ]);

      // creating label for agent
      $this->createFormFieldLabel($formField, 'agent');

      // creating label for user
      $this->createFormFieldLabel($formField, 'user');

    }

    /**
     * Creates Label for billing
     * @param  FormField $formField
     * @param  string $meantFor
     * @return null
     */
    private function createFormFieldLabel($formField, $meantFor)
    {
        $formField->labels()
          ->updateOrCreate(['label'=>'Select Package', 'meant_for'=>$meantFor],
            ['label'=>'Select Package', 'language'=>'en', 'meant_for'=>$meantFor]
          );
    }

    /**
     * Updates unique key for form fields
     * USE-CASE: frontend sends keys to the backend by computing it
     * by itself. In case of custom fields it always sends custom_
     * but in cases where a plugin needs to create a custom field but
     * it doesn't want to store it as a custom field, it has to
     * send another key
     *
     * Adding another field called deletable
     * USE-CASE : whatever a plugin injects a formfield,
     * its on plugin if it wants to to be deletable from
     * form-builder or not
     * @return null
     */
    private function updateUniqueKeyForFormFields()
    {
      $formFields = FormField::get();

      foreach ($formFields as $formField) {
        if($formField->default){
          $formField->unique = $this->getKeyForDefaultFormFields($formField->title);
          $formField->is_deletable = false;
          $formField->is_edit_visible = $this->isEditVisible($formField->title);
        } else {
          $formField->unique = "custom_$formField->id";
          $formField->is_deletable = true;
        }
        $formField->save();
      }
    }

    /**
     * If field is visible in edit ticket
     * @return bool
     */
    private function isEditVisible(string $fieldTitle)
    {
      // few columns are there which should not be visible in edit ticket (since those are not implemented)
      // CC and description
      $fieldsNotToBeEdited = ['Description', 'CC'];
      return !in_array($fieldTitle, $fieldsNotToBeEdited);
    }

    /**
     * gets the unique key for default fields
     * @param  string $title
     * @return string
     */
    private function getKeyForDefaultFormFields(string $title) : string
    {
      switch ($title) {
        case "Help Topic":
        case "Department":
        case "Status":
        case "Source":
        case "Location":
        case "Type":
        case "Priority":
        case "Assigned":
          return strtolower(str_replace(' ','_',$title)) . '_id';

        default:
          return strtolower(str_replace(' ','_',$title));
      }

    }

    /**
     * NOTE: this has been moved from installer to seeder, so that
     * it install passport while updating the system too
     * Installs passport
     * @return void
     */
    private function passportInstall()
    {
      Artisan::call('passport:install', ['--force' => true]);
    }

    
    /*
    *This method update mergeticket display order
    */

    private function statusSeeder()
    {
      Ticket_status::where('name','Merged')->where('order',7)->update(['order'=>8]);
    }

     /**
     * Add cdn settings entry to CommonSettings
     * @return void
     */
    private function cdnSettingsSeeder()
    {
        $checkCdnSettings =CommonSettings::where('option_name','cdn_settings')->count();
        //check cdn value present in common settings table or not
        if(!$checkCdnSettings){

            CommonSettings::create(['option_name' => 'cdn_settings', 'option_value' => 0]);
        }
    }
    /*   
    * This method delete mail-fetch folder inside public directory
    */
    private function deletePublicMailFetchFolder()
    {
          \File::deleteDirectory(public_path('mail-fetch'));
    }
}
