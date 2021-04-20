<?php

if (!function_exists('autoAssignGetOptions'))
{
    /**
     * get the value from db table `common settings` for auto assign module
     * @param string $field
     * @return boolean
     */
    function autoAssignGetOptions($field)
    {
        $common = new App\Model\helpdesk\Settings\CommonSettings();
        $status = $common->getOptionValue('auto_assign', $field);
        return $status;
    }

}
if (!function_exists('isAutoAssign'))
{
    /**
     * check auto assign on/off
     * @return boolean
     */
    function isAutoAssign()
    {
        $schema = autoAssignGetOptions('status');
        $check  = false;
        if ($schema && $schema->option_value == 1)
        {
            $check = true;
        }
        return $check;
    }

}
if (!function_exists('isOnlyLogin'))
{
    /**
     * check auto assign only for logged in agents
     * @return boolean
     */
    function isOnlyLogin()
    {
        $schema = autoAssignGetOptions('only_login');
        $check  = false;
        if ($schema && $schema->option_value == 1)
        {
            $check = true;
        }
        return $check;
    }

}
if (!function_exists('isAssignIfNotAccept'))
{
    /**
     * check auto assgnment is for non accept agents
     * @return boolean
     */
    function isAssignIfNotAccept()
    {
        $schema = autoAssignGetOptions('assign_not_accept');
        $check  = false;
        if ($schema && $schema->option_value == 1)
        {
            $check = true;
        }
        return $check;
    }

}
if (!function_exists('thresold'))
{
    /**
     * get maximum number of ticket can assign to an agent
     * @return integer
     */
    function thresold()
    {
        $schema = autoAssignGetOptions('thresold');
        $check  = "";
        if ($schema && $schema->option_value)
        {
            $check = $schema->option_value;
        }
        return $check;
    }

}

if (!function_exists('isAssignWithType'))
{

    function isAssignWithType()
    {
        $schema = autoAssignGetOptions('assign_with_type');
        $check  = false;
        if ($schema && $schema->option_value == 1)
        {
            $check = true;
        }
        return $check;
    }

}
if (!function_exists('isAssignWithLocation'))
{
    function isAssignWithLocation()
    {
        $schema = autoAssignGetOptions('is_location');
        $check  = false;
        if ($schema && $schema->option_value == 1)
        {
            $check = true;
        }
        return $check;
    }
}

if (!function_exists('deptAssignOption'))
{
    function deptAssignOption()
    {
        $schema = autoAssignGetOptions('assign_department_option');
        $check  = 'all';
        if ($schema && $schema->option_value != 'all')
        {
            $check = $schema->option_value;
        }
        return $check;
    }
}