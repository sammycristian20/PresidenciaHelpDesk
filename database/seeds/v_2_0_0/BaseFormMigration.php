<?php

namespace database\seeds\v_2_0_0;

use database\seeds\DatabaseSeeder as Seeder;
use DB;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Form\FormField;
use Illuminate\Support\Facades\Schema;
use App\Model\helpdesk\Form\CustomFormValue;

class BaseFormMigration extends Seeder
{

  /**
   * Handles updation of labels for agent/client
   * @param  Object $form      old form object
   * @param  FormField $formField
   * @return null
   */
  protected function handleLabels($form, $formField)
  {
    // handle labels and options
    if(isset($form->agentlabel)){
      $this->updateLabelsForAgent($form->agentlabel, $formField);
    }

    if(isset($form->clientlabel)){
      $this->updateLabelsForUser($form->clientlabel, $formField);
    }
  }

  /**
   * updates or creates client labels
   * @param array $labels          client labels
   * @param FormField $formField   instance of the field which is getting updated
   * @return null
   */
  protected function updateLabelsForUser(array $labels, FormField $formField)
  {
    // loop through labels and update
    foreach ($labels as $label) {
      // update label by language
      // get label of the form field and updateOrCreate them
      $formField->labelsForFormField()->updateOrCreate(
        ['language'=> $label->language, 'meant_for'=>'form_field'],[
        'label'=> $label->label, 'meant_for'=>'form_field'
      ]);
    }
  }

  /**
   * updates or creates Agent labels
   * @param array $labels          agent labels
   * @param FormField $formField   instance of the field which is getting updated
   * @return null
   */
  protected function updateLabelsForAgent(array $labels, FormField $formField)
  {
    // loop through labels and update
    foreach ($labels as $label) {
      // update label by language
      // get label of the form field and updateOrCreate them
      $formField->labelsForFormField()->updateOrCreate(
        ['language'=> $label->language, 'meant_for'=>'form_field'],[
        'label'=> $label->label, 'meant_for'=>'form_field'
      ]);
    }
  }

  protected function updateOptions(array $options, FormField $formField)
  {

      foreach ($options as $option) {
        $newOption = $formField->options()->create();

        foreach($option->optionvalue as $label) {
          $a = $newOption->labels()->updateOrCreate(
            ['meant_for'=>'option', 'language'=>$label->language],
            ['label'=>$label->option]
          );

          if(!isset($option->nodes)){
            continue;
          }

          // if it has nodes (child fields)
          foreach ($option->nodes as $form) {

            // go back to creating form field
            $parentQuery = $newOption->nodes();
            $this->updateFormFields($form, $parentQuery);
          }
        }
      }

  }

  /**
   * Update/Creates a form field
   * @param  object $formField   the field that is needed to be created
   * @param  QueryBuilder $parentQuery  query under which formField has to be created
   * @return null
   */
  protected function updateFormFields($form, &$parentQuery)
  {
      $formField = $parentQuery->create([
        'title'=> $form->title,
        'type'=> $form->type,
        'display_for_agent'=> $form->agentDisplay,
        'display_for_user' => $form->customerDisplay,
        'required_for_agent'=> $form->agentRequiredFormSubmit,
        'required_for_user'=> $form->customerRequiredFormSubmit,
      ]);

      if(Schema::hasColumn('form_fields', 'unique')){
        //adding unique to it so that it can be used for ticket data migration
        $formField->unique = $form->unique;

        $formField->save();
      }

      $this->handleLabels($form, $formField);

      if(isset($form->options)){
        $this->updateOptions($form->options, $formField);
      }
      return $formField;
  }

  /**
   * Updates fields display_for_agent, display_for_user, required_for_agent, required_for_user
   * @param FormField $formFieldQuery parent form field query
   * @param Object $oldFormObject
   * @return null
   */
  protected function updateFieldVisibilty(FormField $formField, $oldFormObject)
  {
    $formField->update([
      'display_for_agent'=> $oldFormObject->agentDisplay,
      'display_for_user' => $oldFormObject->customerDisplay,
      'required_for_agent'=> isset($oldFormObject->agentRequiredFormSubmit)
              ? $oldFormObject->agentRequiredFormSubmit : $oldFormObject->agentDisplay,
      'required_for_user'=> isset($oldFormObject->customerRequiredFormSubmit)
              ? $oldFormObject->customerRequiredFormSubmit : $oldFormObject->customerDisplay,
    ]);
  }

  /**
   *
   * @param  string $uniqueKey      unique string mapped to form field in old DB structure
   * @param  string|int $value      value of the field
   * @param  int $customId          id of the model
   * @param  string $customType     namespace of the model
   * @return null
   */
  protected function updateInCustomFormValueTable($uniqueKey, $value, $customId, $customType)
  {
    $formField = FormField::where('default', 0)->where('unique', $uniqueKey)
      ->select('id', 'type')->first();

    if($formField && $value){
      CustomFormValue::create([
        'form_field_id' => $formField->id,
        'value' => $this->getFormattedValue($formField->type, $value),
        'custom_id'=> $customId,
        'custom_type'=> $customType
      ]);
    }
  }

  /**
   * Formats value in required format. For eg, if value is a checkbox, it
   * converts that into array.
   * @param int $formFieldType
   * @param string $value value of the field
   * @return string|array
   */
  protected function getFormattedValue($formFieldType, $value)
  {
      // get type of form and if it is checkbox
      if($formFieldType == 'checkbox'){
        return explode(',', $value);
      }
      return $value;
  }
}
