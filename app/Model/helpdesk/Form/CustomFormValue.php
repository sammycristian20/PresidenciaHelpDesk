<?php

namespace App\Model\helpdesk\Form;

use App\Http\Controllers\Common\TicketsWrite\SlaEnforcer;
use App\Model\helpdesk\Ticket\Tickets;
use Exception;
use Illuminate\Database\Eloquent\Model;
use App;
use Logger;
use App\Traits\Observable;

class CustomFormValue extends Model
{
    use Observable;

    protected $table = 'custom_form_value';

    protected $fillable = [
        'form_field_id',
        'value',
        'custom_id',
        'custom_type',
        'type'
    ];

    protected $appends = ['label', 'field_type'];

    /**
     * Appends label to fields
     */
    public function getLabelAttribute()
    {
        $lang = App::getLocale();

        $label = FormFieldLabel::where('labelable_id', $this->form_field_id)
          ->where('labelable_type', 'App\Model\helpdesk\Form\FormField')
          ->where('meant_for', "form_field")
          ->where(function ($q) use ($lang) {
            // either user selected language. if that not present, then any which is non-empty
            $q->where('language', $lang)->orWhere('language', '!=', '');
          })->value('label');

        return $label;
    }

    public function formfieldName()
    {
        return $this->hasOne('App\Model\helpdesk\Form\FormField', 'id', 'form_field_id')->select('id', 'title');
    }

    public function custom()
    {
        return $this->morphTo();
    }

    public function setValueAttribute($value)
    {
        // validation isn't required for an API field
        $fieldType = FormField::whereId($this->form_field_id)->where('title', '!=', 'Api')->value('type');

        if ($value && !$this->isValidFormValue($fieldType, $value)) {
            throw new Exception("invalid value ". json_encode($value). " for field type $fieldType");
        }

        $this->attributes['value'] = json_encode($value);
    }

    /**
     * Checks if the value passed is a valid form value or not
     * @param string $type
     * @param string|array $value
     * @return bool
     */
    public function isValidFormValue($type, $value)
    {
        switch ($type) {
            case 'radio':
            case 'select':
                $optionsIds = FormFieldOption::where('form_field_id', $this->form_field_id)->pluck('id')->toArray();
                return (bool) FormFieldLabel::where('meant_for', 'option')
                    ->where('label', $value)
                    ->whereIn('labelable_id', $optionsIds)
                    ->count();

            case 'checkbox':
                if (is_array($value)) {
                    $optionsIds = FormFieldOption::where('form_field_id', $this->form_field_id)->pluck('id')->toArray();
                    $optionCount = FormFieldLabel::where('meant_for', 'option')
                        ->whereIn('label', $value)
                        ->whereIn('labelable_id', $optionsIds)
                        ->count();
                    return $optionCount == count($value);
                }
                return false;

            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL);

            case 'number':
                return is_numeric($value);

            default:
                return true;
        }
    }

    public function getValueAttribute($value)
    {

        try {
            return json_decode($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * gets custom fields based on the parent query (`User`,`Tickets` or `Organization`)
     * @param object $parent either User, Tickets or Organisation object
     * @return Collection
     */
    public static function getCustomFields($parent)
    {
        $customFields = $parent->customFieldValues()->get()->toArray();
        $formattedCustomFieldArray = [];
        foreach ($customFields as $customField) {
            $formattedCustomFieldArray['custom_'. $customField['form_field_id']] = $customField['value'];
        }
        return $formattedCustomFieldArray;
    }

    /**
     * creates or update custom field entries in form_field_value table
     * @param array $customFields array of custom fields with each element in format `custom_`
     * @param $parent
     * @return null
     */
    public static function updateOrCreateCustomFields($customFields, $parent)
    {
        //filtering only those fields which has `custom_` prefix
        $customFieldArray = array_filter($customFields, function ($k) {
            return strpos($k, "custom_") !== false;
        }, ARRAY_FILTER_USE_KEY);

        try {
            $parent->customFieldValues()->delete();

            $attachmentFields = FormField::where('type', 'file')->pluck('id')->toArray();

          // find out which field is without parent and remove that field from array
          // for eg. custom_1.. Check if it is there in form_field_option table. If yes,
          // then, get its option Id. query for that optionId in form_field table.
          // get its form_field_id. Now, if that form_field_id is not present in the request, remove the child
            foreach ($customFieldArray as $key => $value) {
                $formFieldId = str_replace("custom_", "", $key);

                // given field is an attachment field, do not save the value
                if (in_array($formFieldId, $attachmentFields)) {
                    continue;
                }


                if($value){
                    $fieldType = FormField::find($formFieldId)->type;

                    if ($fieldType == 'date' && $value) {
                        $value = str_replace('+', ' ', $value);
                    }

                    $parent->customFieldValues()->updateOrCreate(['form_field_id'=> $formFieldId], [
                        'form_field_id'=> $formFieldId,
                        'value' => $value
                    ]);
                }
            }

            if ($parent instanceof Tickets) {
                // writing it here instead of in `afterModelActivity` to avoid duplicate SLA calls when
                // each custom field is created
                (new SlaEnforcer($parent))->handleSlaRelatedUpdates();
            }
        } catch (\Exception $e) {
            Logger::exception($e);
        }
    }

    /**
     * Append field type
     */
    public function getFieldTypeAttribute()
    {
        return FormField::whereId($this->form_field_id)->value('type');
    }
}
