<?php

namespace App\Http\Requests\helpdesk\Organisation;

use App\Traits\CustomFieldBaseRequest;
use App\Traits\RequestJsonValidation;
use App\Http\Requests\Request;
use App\Model\helpdesk\Agent_panel\Organization;
use Auth;
use Lang;

/**
 * Base class which contains custom validations for create and edit organisation
 * @author  avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class BaseOrganisationRequest extends Request
{
    use CustomFieldBaseRequest, RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::user() && Auth::user()->role != 'user'){
          return true;
        }

        return false;
    }

    /**
     * Creating a custom validation for domain unique-ness
     * @return Validator
     */
    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();
        $validator->addImplicitExtension('unique_domain', function($attribute, $value, $parameters) {
          if (is_array($value)) {
              foreach ($value as $listOfDomain) {
                $doesDomainExist = Organization::where('domain', '!=', "")
                            ->where('id', '!=', $this->id)
                            ->whereRaw('FIND_IN_SET(?,domain)', [$listOfDomain])
                            ->pluck('id as org_id')->toArray();
                if ($doesDomainExist) {
                    return false;
                }
              }
          }
          return true;
        });

        $validator->addImplicitExtension('valid_domain', function($attribute, $value, $parameters) {
          if (is_array($value)) {
            foreach ($value as $domain) {
              if(!preg_match("/^([A-Z0-9.-]+\.[A-Z]{2,5})$/i", $domain)){
                return false;
              }
            }
            return true;
          }
          return true;
        });

        return $validator;
    }

    /**
     * Adding custom message for domain unique-ness
     * @return Array
     */
    public function messages()
    {
        return [
            'unique_domain' => Lang::get('lang.domain_name_already_taken'),
            'valid_domain' => Lang::get('lang.invalid_domain_format')
        ];
    }
}
