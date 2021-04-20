<?php

namespace App\Traits;

use App\Model\helpdesk\Form\CustomFormValue;
use App\User;

trait TicketKeyMutator
{
    /**
     * Formats actions array according to tickets table format.
     * NOTE: it also appends requester information to tickets array
     * @param  array  $actions  workflow actions array
     * @return null
     */
    protected function formatTicketsArrayFromOldToNewKey(array &$ticketValuesArray)
    {
        foreach ($ticketValuesArray as $key => $value) {
            $newKey = $this->transformTicketKey($key, true);
            $this->renameKey($key, $newKey, $ticketValuesArray);
        }


        // if user_id is present in the array, it will append all user_details to it
        if(isset($ticketValuesArray['user_id'])){
            // userId could be extracted in the method itself but passing it separately is more readable
            $this->requester = $this->getUserDetails($ticketValuesArray['user_id']);
            $ticketValuesArray = array_merge($ticketValuesArray, $this->requester);
        }
    }

    /**
     * Formats actions array according to tickets table format
     * NOTE: it also remove keys which belongs to requester, so that it doesn't get
     * appended to ticket
     * @param  array  $actions  workflow actions array
     * @return null
     */
    protected function formatTicketsArrayFromNewToOldKey(array &$ticketValuesArray)
    {
        foreach ($ticketValuesArray as $key => $value) {
            $newKey = $this->transformTicketKey($key);
            $this->renameKey($key, $newKey, $ticketValuesArray);
        }

        if(is_array($this->requester))
            // removing requester keys from array
            $ticketValuesArray = array_diff_key($ticketValuesArray, $this->requester);
    }

    /**
     * gets tickets table key based on passed key
     * @param  string $requestKey
     * @return string             old key which is used in ticket array
     */
    private function transformTicketKey(string $key, bool $transformFromOldToNew = false) : string
    {
        // mapping for request keys to tickets key. New key to old key mapping
        $ticketKeyMapper = [
            'status_id' => 'status',
            'sla_id' => 'sla',
            'source_id' => 'source',
            'department_id' => 'dept_id',
            'assigned_id' => 'assigned_to',
            'type_id' => 'type',
            'description'=>'body',
        ];

        // making old key to new key mapping if $transformFromOldToNew is true
        if($transformFromOldToNew){
            $ticketKeyMapper = array_flip($ticketKeyMapper);
        }

        if(isset($ticketKeyMapper[$key])){
            return $ticketKeyMapper[$key];
        }

        return $key;
    }

    /**
     * Renames old key into new in the array
     * @param  string $oldKey
     * @param  string $newKey
     * @param  array  $array
     * @return null
     */
    private function renameKey(string $oldKey, string $newKey, array &$array)
    {
        if(array_key_exists($oldKey, $array)){
            $array[$newKey] = $array[$oldKey];
            if($oldKey != $newKey){
                unset($array[$oldKey]);
            }
        }
    }

    /**
     * Gets User details and formats it in a way that is required by workflow/listener
     * @param  int $userId Id of the user
     * @return array
     */
    protected function getUserDetails(int $userId)
    {
        // default details
        $userDefault = User::where('id', $userId)->select('id', 'email', 'first_name', 'last_name',
            'country_code', 'phone_number as work_phone', 'mobile as mobile_phone', 'user_name', 'internal_note as address')->first();

        // custom details
        $userCustom = $this->getUserCustomFields($userId);

        $organisationIds = $userDefault->getUsersOrganisations()->pluck('org_id')->toArray();

        $organisationDepartmentId = $userDefault->getUsersOrganisations()->where('org_department', '!=', null)->value('org_department');

        $userDefault->organisation = $organisationIds;

        $userDefault->organisation_department = $organisationDepartmentId;

        $userDefault = $userDefault->toArray();

        return array_merge($userDefault, $userCustom);
    }

    /**
     * gets user custom fields in formatted way
     * @param $userId
     */
    private function getUserCustomFields($userId)
    {
        return CustomFormValue::where('custom_id', $userId)->where('custom_type','App\User')
            ->select('form_field_id', 'value')->get()
            // mapping it to convert it into custom_ format
            ->map(function($data){
                return [ "custom_" . $data->form_field_id => $data->value ];
            })->collapse()->toArray();
    }
}