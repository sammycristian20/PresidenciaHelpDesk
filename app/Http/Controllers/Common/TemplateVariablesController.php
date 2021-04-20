<?php

namespace App\Http\Controllers\Common;

//controllers
use App\Http\Controllers\Controller;
// models
use App\Model\helpdesk\Settings\Company;
use App\Model\helpdesk\Settings\System;
use App\Model\Common\TemplateShortCode;

//classes
use Finder;
use Lang;

/**
 * |======================================================
 * | Class Template Variables Controller
 * |======================================================
 * This controller is used to get all the varables used in Email/SMS templates
 * @author manish.verma@ladybirdweb.com
 * @copyright Ladybird Web Solutions
 */
class TemplateVariablesController extends Controller
{
    /**
     * @category function to replace template variable with values
     * @param array $template_variables (list of template variables)
     * @return array $variables (value of replaced template variables)
     */
    public function getVariableValues($template_variables, $content = '')
    {
        $variables = [];
        if (checkArray('message_content', $template_variables) != '') {
            $content = checkArray('message_content', $template_variables);
        }
        $company_name = $this->checkElement('company_name', $template_variables);
        if ($company_name === "") {
            $company_name = $this->company();
        }

        $system_from = $this->checkElement('system_from', $template_variables);
        if ($system_from == '') {
            $system_from = $this->system();
        }

        $system_link = $this->checkElement('system_link', $template_variables);
        if ($system_link === "") {
            $system_link = $this->system('link');
        }

        $company_link = $this->checkElement('company_link', $template_variables);
        if ($company_link === "") {
            $company_link = $this->company('link');
        }
        foreach (TemplateShortCode::pluck('key_name', 'shortcode')->toArray() as $key => $value) {
            $variables[$key] = $this->checkElement($value, $template_variables);
        }

        $variables['{!! $system_from !!}'] = $system_from;
        $variables['{!! $system_link !!}'] = $system_link;
        $variables['{!! $company_name !!}'] = $company_name;
        $variables['{!! $company_link !!}'] = $company_link;
        $variables['{!! $message_content !!}'] = $content;

        return $variables;
    }

    /**
     * @category function to return list of avaialable variables for templates
     * @var array $variables
     * @return array $variables list of available variables
     */
    public function getAvailableTemplateVariables()
    {
        $variables = [];
        $baseQuery = TemplateShortCode::whereNull('plugin_name');
        \Event::dispatch('update_template_variable_shortcode_query_builder', [$baseQuery]);
        foreach ($baseQuery->pluck('description_lang_key', 'shortcode')->toArray() as $key => $value) {
            $variables[$key] = $value;
        }

        return $variables;
    }

    public function checkElement($element, $array)
    {
        $value = "";
        if (is_array($array)) {
            if (key_exists($element, $array)) {
                $value = $array[$element];
            }
        }
        return $value;
    }

    /**
     * Fetching comapny name to send mail.
     * @var $fetch string to identify what needs to be fetched
     * @return string
     */
    public function company($fetch = 'name')
    {
        return persistentCache('company', function() use ($fetch){
            if($fetch == 'name'){
                $companyName = Company::where('id', '=', '1')->value('company_name');
                return $companyName ?: 'Support Center';
            } else {
                return Company::where('id', '=', '1')->value('website');
            }
        }, 30, [$fetch]);
    }

    /**
     * system.
     *
     * @param string $fetch
     * @return string
     */
    public function system($fetch = 'name')
    {
        return persistentCache('system', function() use ($fetch){
            if($fetch == 'name'){
                $systemName = System::where('id', '=', '1')->value('name');
                return $systemName ?: 'Support Center';
            } else {
                return url('/');
            }
        }, 30, [$fetch]);
    }
}