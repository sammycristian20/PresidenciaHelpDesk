<?php

namespace App\Traits;

/**
 * Contains custom field request methods for validation
 */

use App\Model\helpdesk\Settings\CommonSettings;
use Cache;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Form\FormField;
use App\Rules\CaptchaValidation;
use App\Policies\TicketPolicy;
use Illuminate\Http\Exceptions\HttpResponseException;

trait CustomFieldBaseRequest
{

    /**
     * Source of ticket creation. Possible values (`agent_panel`, `client_panel`, `recur`)
     * @var string
     */
    private $source;

    private $isEditMode;

    /**
     * unique key of the fields for which validation is not required (for eg. status_id, help_topic_id, custom_1)
     * USE CASE: description validation is not required in case of edit and fork ticket
     * @var array
     */
    private $validateExcept = [];

    /**
     * gets all the fields with its validation
     * @param  string $category  category of form(`ticket`, `user`, `organization`)
     * @param  string $source    source from where request is coming `client_panel`, `agent_panel`
     * @param  array $validateExcept fields except which everything should be validated
     * @return array
     */
    protected function fieldsValidation(string $category, string $source, array $validateExcept = [], $isEditMode = false) : array
    {
        $this->source = $source;
        $this->validateExcept = $validateExcept;

        $this->isEditMode = $isEditMode;
        // return array_merge(
        //  $this->defaultFieldsValidation($category),
        //  // $this->customFieldsValidation($category)
        // );
        return $this->defaultFieldsValidation($category);
    }

    /**
     * Gives the list of default fields along with its validation
     * @param  string $category  category of form(`ticket`, `user`, `organization`)
     * @return array
     */
    protected function defaultFieldsValidation(string $category) : array
    {
        $requiredFields = $this->getRequiredFields($category, 'default');

        $validationArray = [];

        foreach ($requiredFields as $key) {
            $key = str_replace(' ', '_', strtolower($key));

            // do not validate fields which are there in validateExcept array
            if (in_array($key, $this->validateExcept)) {
                continue;
            }
            $validationArray[$key] = ['required'];
        }

        return $validationArray;
    }

    /**
     * Gives the list of custom fields along with its validation
     * @param  string $category  category of form(`ticket`, `user`, `organization`)
     * @return array
     */
    protected function customFieldsValidation(string $category) : array
    {
        $requiredFields = $this->getRequiredFields($category, 'custom');

        $validationArray = [];

        foreach ($requiredFields as $key) {
            $validationArray['custom_'.$key] = 'required';
        }

        return $validationArray;
    }

    /**
     * gets array of required field keys
     * @param  string $category category of form(`ticket`, `user`, `organization`)
     * @param  string $type     type of fields(`default` or `custom`)
     * @return array            array of required keys
     */
    protected function getRequiredFields(string $category, string $type) : array
    {
        //caching the form category id
        $formCategoryId = Cache::rememberForever($category.'_form_id', function () use ($category) {
            return FormCategory::where('category', $category)->first()->id;
        });

        $userRole = $this->source == 'agent_panel' ? 'agent' : 'user';

        $baseQuery = FormField::where('category_id', $formCategoryId)
            ->where("required_for_$userRole", 1)->where("display_for_$userRole", 1)->where('is_active', 1);

        if($this->isEditMode){
            $baseQuery->where('is_edit_visible', 1);
        }

        $requiredFields = ($type == 'default') ? $baseQuery->where('default', 1)->pluck('unique')
        : $baseQuery->where('default', '!=', 1)->pluck('id');

        return $requiredFields->toArray();
    }


  /**
   * Moves all custom attachments to `attachments` key
   * @param array $request
   * @return null
   */
    protected function getFormattedParameterWithAttachments(array $parameters)
    {
      //filtering only those fields which has `custom_` prefix
        $customFieldArray = array_filter($parameters, function ($k) {
              return strpos($k, "custom_") !== false;
        }, ARRAY_FILTER_USE_KEY);

        $attachments = isset($parameters['attachments']) ? $parameters['attachments'] : [];

        foreach ($customFieldArray as $key => $value) {
            $unsetKey = false;
          // if value is an array
            if (is_array($value)) {
                foreach ($value as $element) {
                    if (is_array($element)) {
                        array_push($attachments, $element);
                        $unsetKey = true;
                    }
                }

                if ($unsetKey) {
                    unset($parameters[$key]);
                }
            }
        }
        $parameters['attachments'] = $attachments;
        return $parameters;
    }

    /**
     * Checks if the logged in user has ticket assign permission or not
     * @param integer $ticketAssigneeId
     * @return null
     * @throw HttpResponseException
     */
    private function checkTicketAssignPermission($ticketAssigneeId)
    {
      // sending message to status code 400 becasue it need to displayed at beginning of form
        if ($ticketAssigneeId && !(new TicketPolicy)->assign()) {
            throw new HttpResponseException(errorResponse(trans('lang.if_you_want_to_assign_ticket_then_kindly_get_assign_ticket_permission'), 400));
        }
    }

    /**
     *
     * @param $category
     * @param $scenario
     * @param $panel
     * @param $validationArray
     * @return void
     */
    protected function captchaValidation($category, $scenario, $panel, &$validationArray)
    {
        $needle = $category.'_'.$scenario.'_'.$panel;
        $recaptchaSettings = CommonSettings::where('option_name', 'google')->pluck('option_value', 'optional_field')->toArray();

        $isCaptchaValidationRequired = false;

        if ($recaptchaSettings && $recaptchaSettings['recaptcha_status']) {
            $isCaptchaValidationRequired = strpos($recaptchaSettings['recaptcha_apply_for'], $needle) !== false;
        }

        if ($isCaptchaValidationRequired) {
            $validationArray['g-recaptcha-response'] = ['required', new CaptchaValidation()];
        }
    }
}
