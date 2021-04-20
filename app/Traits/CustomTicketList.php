<?php

namespace App\Traits;

use Lang;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Model\helpdesk\Settings\Ticket as TicketSetting;
use Cache;
use Config;

/**
 * contains all the custom modification methods required in TicketListController
 */
trait CustomTicketList
{

    /**
     * Appends extra field to the ticket according to tickets settings
     * @param  QueryBuilder $ticketsQuery base query of the ticket
     * @return QueryBuilder Tickets Query
     */
    protected function getExtraFieldsIfRequired(QueryBuilder $ticketsQuery) : QueryBuilder
    {
        //this has to be fetched from ticket settings
        $settingsTicket = Cache::rememberForever('settings_ticket', function(){
            return TicketSetting::select('show_status_date', 'show_org_details', 'custom_field_name', 'count_internal')->first();
        });

        return $ticketsQuery->when($settingsTicket->show_status_date, function($query){
                $query->with('lastStatusActivity:id,ticket_id,updated_at');
            })
            ->when($settingsTicket->show_org_details, function($query){
                $query->with('user.organizations:organization.id,name,address');
            })
            ->when($settingsTicket->custom_field_name, function($query) use ($settingsTicket){
                $query->with(['customFieldValues'=> function($sq) use ($settingsTicket){
                    $sq->whereIn('form_field_id', $settingsTicket->custom_field_name);
                }]);
            });
    }

	/**
     * Remove all extra fields from their default locations and push that into `extra_data`
     * with `label` and `value`
     * @param  array &$ticket  ticket in key value form
     * @return  null
     */
    protected function formatExtraFields(array &$ticket)
    {

        $ticket['extra_fields'] = [];

    		$this->formatLastStatusActivity($ticket);

    		$this->formatOrganizationDetails($ticket);

            $this->formatFormFieldData($ticket);
            
            $this->formatTicketLocation($ticket);
    }

    /**
     * If the `show_user_location` bit is set in ticket settings
     * adds the location to extra fields array.
     * @param $ticket
     */
    private function formatTicketLocation(&$ticket)
    {
        $locationCanBeShown = TicketSetting::value('show_user_location');

        if ((bool) $locationCanBeShown && $ticket['location']) {
            array_push($ticket['extra_fields'], ['label' => trans('lang.location'),'value' => $ticket['location']['name']]);
        }
    }

    /**
     * formats last status activity by removing `last_status_activity` key
     * and pushing it to 'extra_field' key of ticket
     * @param  array &$ticket ticket data in key value form
     * @return null
     */
    private function formatLastStatusActivity(array &$ticket)
    {
        if(array_key_exists('last_status_activity', $ticket)){
	        $extraField = [];

            //last_status_activity
            if(isset($ticket['last_status_activity']['updated_at'])){
                $extraField['label'] = Lang::get('lang.last_status_activity');
                $extraField['value'] = faveoDate($ticket['last_status_activity']['updated_at']);
                array_push($ticket['extra_fields'], $extraField);
            }
            unset($ticket['last_status_activity']);
        }
    }

    /**
     * formats organizations by removing `organizations` key
     * and pushing it to 'extra_field' key of ticket
     * @param  array  &$ticket
     * @return null
     */
    private function formatOrganizationDetails(array &$ticket)
    {
    	if(isset($ticket['from']['organizations'])){
            //if organization exists
            foreach ($ticket['from']['organizations'] as $organization) {
        		    $extraField = [];
                $extraField['label'] = Lang::get('lang.organization');

                $organizationProfilePath = Config::get('app.url').'/organizations'."/".$organization['id'];

                $address = $organization['address'] ? ', '.strip_tags($organization['address']) : '';

                $linkToOrganization = "<a href=".$organizationProfilePath." target='_blank'>".$organization['name']."</a>";

                $extraField['value'] = $linkToOrganization.$address;
                array_push($ticket['extra_fields'], $extraField);
            }

            unset($ticket['from']['organizations']);
        }
    }

    /**
     * formats form field data by removing `selected_form_data` key
     * and pushing it to 'extra_field' key of ticket
     * @param  array  &$ticket
     * @return null
     */
    private function formatFormFieldData(array &$ticket)
    {

        if(isset($ticket['custom_field_values'])){
            //if custom fields are present
            foreach ($ticket['custom_field_values'] as $formData) {
                array_push($ticket['extra_fields'], $formData);
            }
            unset($ticket['custom_field_values']);
        }
    }

}
