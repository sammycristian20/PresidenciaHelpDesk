<?php

namespace App\Http\Controllers\Client\helpdesk;

use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketListController;
use Illuminate\Http\Request;
use App\Model\helpdesk\Ticket\Tickets;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Model\helpdesk\Settings\CommonSettings;
use Cache;
use Auth;

/**
 * ClientTicketListController uses the all the parameters of TicketListController and TicketsCategoryController
 */
class ClientTicketListController extends TicketListController
{
    public function __construct()
    {
         $this->middleware(['auth']);
    }
    
    /**
     * Gets ticket-list depending upon which parameter is passed (mytickets, inbox, unassigned etc)
     * @return array => success response with filtered tickets
     */
    public function getTicketsList(Request $request)
    {
        //gets no of tickets per page from cache else 10
        $ticketsPerPage = (Cache::has('ticket_per_page')) ? Cache::get('ticket_per_page') : 10;

        $this->setRequest($request);

        $limit = $request->input('limit') ?: $ticketsPerPage;

        $sortField = $request->input('sort-field') ?: 'updated_at';

        $sortOrder = $request->input('sort-order') ?: 'desc';

        $baseQuery = $this->baseQueryForTickets();
        $this->modifyQueryForClient($baseQuery);
        $ticketsQuery = $baseQuery->select('tickets.id', 'tickets.updated_at', 'tickets.user_id', 'ticket_number');
        if (Auth::user()->role != 'user') {
            $ticketsQuery->addSelect('assigned_to', 'team_id')
                ->with('assigned:id,user_name,first_name,last_name,profile_pic,email', 'assignedTeam:id,name');
        }

        $tickets = $ticketsQuery->orderBy($sortField, $sortOrder)->paginate($limit);

        $tickets->getCollection()->transform(function ($ticket) {
            unset($ticket->user_id, $ticket->encrypted_id);
            return $ticket;
        });

        return successResponse('', $tickets);
    }

    /**
     * Appends foreign query to base query.
     * Overriding parent method to query data which are required in client panel
     * @param $ticketsQuery
     * @return mixed
     */
    protected function appendForeignKeyQuery($ticketsQuery)
    {
        return $ticketsQuery->with([
            'user:id,user_name,first_name,last_name,profile_pic,email',
            'firstThread:id,ticket_id,title',
            'lastThread.user:id,user_name,first_name,last_name,profile_pic,email',
            'collaborator'
        ]);
    }

    /**
     * Gives query for all allowed tickets query for current user for client panel
     * @return object => $tickets
     */
    public function allTicketsQuery()
    {
        $this->user = $this->user ? : Auth::user();

        return Tickets::query();
    }

    /**
     * Function returns id of organization auth user belongs to
     * - If auth user is manager of any organization then id of that organization
     * will be added in return value by default
     * - If auth user is member of organization(s) then based on show org setting
     * id(s) of organization(s) will be added in return value
     *
     * @return  array      An empty array or array containing integer id of organizations
     */
    private function getOrganisationIdOfUser()
    {
        $isOrganizationManagerThenOrganizationIds = $this->user->getUsersorganisations()->where('role', 'manager')->pluck('org_id')->toArray();
        $isOrganizationMemberThenOrganizationIds = $this->user->getUsersorganisations()->where('role', 'members')->pluck('org_id')->toArray();

        $organizationIdsExtraParameters = $this->checkOrganizationTicketsViewableAndSetOrganizationIds($isOrganizationMemberThenOrganizationIds);

        if ($isOrganizationManagerThenOrganizationIds) {
            $organizationIdsExtraParameters = array_merge($organizationIdsExtraParameters, $isOrganizationManagerThenOrganizationIds);
            if ($this->request->has('organization-ids')) {
                return $this->checkOrganizationManagerCanAccessOnlyHisOrganization($isOrganizationManagerThenOrganizationIds);
            }
        }

        return $organizationIdsExtraParameters;
    }


    /**
     * method to check whether the logged in user is organization manager of passed organization id's or not
     * if he is not an organization manager of the passed organization id's then wrong organization id's is set
     * so, that he should not get access to view other's tickets and ticket list is returned empty at the end
     * @param  array $organizationIds
     * @return int
     */
    private function checkOrganizationManagerCanAccessOnlyHisOrganization(array $organizationIds)
    {
        $organizationIdsFromRequest = $this->request->input('organization-ids');
        return array_intersect($organizationIdsFromRequest, $organizationIds) ?: [0];
    }

    /**
     * method to check organization tickets are viewable by logged in user or not and set organization ids
     * @param array $organizationIds
     * @return array $organizationIdsExtraParameters
     */
    private function checkOrganizationTicketsViewableAndSetOrganizationIds(array $organizationIds)
    {
        $isOrganizationTicketsViewable = (bool) CommonSettings::where('option_name', 'user_show_org_ticket')->first()->status;

        $organizationIdsExtraParameters = [];

        if ($isOrganizationTicketsViewable) {
            $organizationIdsExtraParameters = $organizationIds;
        }

        return $organizationIdsExtraParameters;
    }

    /**
     * !! Alert!!
     * This method modified ticket QueryBuilder based on different conditions 
     * to return list of ticket of user's organizations, and the ticket they are
     * collaborating to. As for client we need to show them
     * - their tickets 
     * - tickets of their organization members if they are normal members(based on
     *   settings in the system)
     * - tickets of their organization members if they are manager of organizations
     * - tickets where they are collaborating(based on settings in the system)
     *
     * Note: Refer Tests\Unit\Backend\Client\ClientTicketListControllerTest
     * modifyQueryForClient group
     *
     * @param   QueryBuilder  $baseQuery
     * @return  void
     */
    private function modifyQueryForClient(&$baseQuery):void
    {
        $allowCC = commonsettings('user_show_cc_ticket','','status');
        $organizationId = $this->getOrganisationIdOfUser();
        if(!$organizationId && $this->request->input('organization-ids')) {
            $organizationId = ['0'];
        }
        $baseQuery->where(function($query) use($allowCC, $organizationId){
            $query->when(!$this->request->input('organization-ids'),function($query){
                $query->where('user_id',$this->user->id);
            })->when($allowCC,function($query){
                $query->orWhereHas('collaborator', function($query){
                    $query->where('user_id', $this->user->id);
                });
            })->when($organizationId, function($query) use($organizationId){
                $query->orWhereHas('user.organizations', function($query)use($organizationId){
                        $query->whereIn('organization.id', [$organizationId]);
                });
            });
        });
    }

    /**
     * Gets ticket count by ticket-list parameters mentioned in doc block of class
     * @param  array $parameters should be in key value pair. for eg. ['category'=>'inbox']
     * @return int
     */
    public function getTicketCountByParameters(array $parameters = []) : int
    {
        $this->request = $this->request ?: new Request;

        $this->request->replace($parameters);

        $baseQuery = $this->baseQueryForTickets(false);

        $this->modifyQueryForClient($baseQuery);

        return $baseQuery->count();
    }
}
