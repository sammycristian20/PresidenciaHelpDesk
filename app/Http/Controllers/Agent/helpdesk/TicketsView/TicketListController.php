<?php
namespace App\Http\Controllers\Agent\helpdesk\TicketsView;

use Illuminate\Http\Request;
use App\Model\helpdesk\Filters\Filter;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketsCategoryController;
use App\Model\helpdesk\Ticket\Ticket_Form_Data;
use App\Model\helpdesk\Filters\Tag;
use App\Model\helpdesk\Filters\Label;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Settings\Ticket as TicketSetting;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Carbon;
use Cache;
use App\Traits\CustomTicketList;
use App\Model\helpdesk\Ticket\TicketFilter;
use Exception;
use App\Model\helpdesk\Form\FormField;
use App\Traits\FaveoDateParser;
use UnexpectedValueException;
use Event;

/**
 * Handles tickets list view by filtering/searching/arranging tickets
 * USAGE :
 * Request can have following parameters:
 *
 *               NAME          |        Possible values
 * category (string, required) : all, inbox, mytickets, closed, unassigned, deleted, unapproved
 * search-query (string, optional) : any string that has to be searched in tickets in the current category
 * sort-order (string, optional) : asc/desc ( ascending/descending ) for sorting. By default its value is 'desc'
 * sort-field (string, optional) : The field that is required to be sorted. By default its value is 'updated_at'
 * limit (string, optional) : Number of tickets that are reuired to display on a partiular page. By default its value is 10
 * page (string, optional) : current page in the ticket list.By default its value is 1
 *
 *
 *
 * ADVANCED SEARCH FILTER
 *
 * helptopic-ids (array, optional)
 * dept-ids (array, optional)
 * priority-ids (array, optional)
 * sla-plan-ids (array, optional)
 * ticket-ids (array, optional)
 * owner-ids (array, optional)
 * assignee-ids (array, optional)
 * team-ids (array, optional)
 * status-ids (array, optional)
 * type-ids (array, optional)
 * source-ids (array, optional)
 * assigned (boolean, optional)
 * answered (boolean, optional)
 * tag-ids (array, optional)
 * label-ids (array, optional)
 * due-on (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
 * created-at (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
 * updated-at (datetime in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
 * closed-at (datetime closing time in agent's timezone in faveo date format (See FaveoDateParser's doc for format), optional)
 * custom_1 (string, optional)
 * creator-ids(array, optional)
 * location-ids(array, optional)
 * organization-ids (array, optional)
 * filter-id(int, optional) // if ticket of a filter is required
 * has-response-sla-met (boolean, optional)
 * has resolution-sla-met (boolean, optional)
 * is-resolved (boolean, optional)
 * resolution-time (string, optional) format: interval::2~minute~4~minute
 * average-response-time (string, optional) format: interval::2~minute~4~minute
 * first-response-time (string, optional) format: interval::2~minute~4~minute
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class TicketListController extends TicketsCategoryController
{
    use CustomTicketList, FaveoDateParser;

    protected $request;

    /**
     * Timezone in which tickets has considered for displaying and filtering
     * @var string
     */
    protected $timezone = 'UTC';

    /**
     * Gets ticket-list depending upon which parameter is passed (mytickets, inbox, unassigned etc)
     * @param Request $request
     * @return array => success response with filtered tickets
     */
    public function getTicketsList(Request $request)
    {
        try {

            //gets no of tickets per page from cache else 10
            $ticketsPerPage = (Cache::has('ticket_per_page')) ? Cache::get('ticket_per_page') : 10;

            $this->setRequest($request);

            if (isset($this->request->filter_id)) {
                return $this->getTicketListByFilterId($this->request->filter_id);
            }

            $limit = $request->input('limit')?: $ticketsPerPage;

            $sortField = $request->input('sort-field') ?: 'updated_at';

            $sortOrder = $request->input('sort-order') ?: 'desc';

            $baseQuery = $this->baseQueryForTickets();

            $tickets = $baseQuery->select('tickets.id', 'tickets.updated_at', 'tickets.created_at', 'tickets.status', 'tickets.user_id', 'assigned_to', 'ticket_number', 'help_topic_id', 'tickets.dept_id', 'tickets.priority_id', 'tickets.source', 'duedate', 'isanswered', 'team_id', 'creator_id', 'location_id')
                ->orderBy($sortField, $sortOrder)
                ->paginate($limit)->toArray();

            $formattedTickets = $this->formatTickets($tickets);

            return successResponse('', $formattedTickets);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Sets request
     * @param Request $request
     * @return void
     */
    public function setRequest(Request $request) : void
    {
      $this->request = $request;
    }

    /**
     * Takes ticket's base query and appends to it search query according to whether that search parameter is present in the request or not
     * @param $ticketsQuery
     * @return object => query
     * @throws Exception
     */
    protected function filteredTickets(QueryBuilder $ticketsQuery) : QueryBuilder
    {
        //tickets created under given help topic(s)
        $this->filteredTicketQueryModifierForArrayFields('helptopic-ids', 'help_topic_id', $ticketsQuery);

        //tickets in a given department(s)
        $this->filteredTicketQueryModifierForArrayFields('dept-ids', 'tickets.dept_id', $ticketsQuery);

        //tickets with given priority(s) eg. low, high etc.
        $this->filteredTicketQueryModifierForArrayFields('priority-ids', 'priority_id', $ticketsQuery);

        //tickets with given sla-plans
        $this->filteredTicketQueryModifierForArrayFields('sla-plan-ids', 'sla', $ticketsQuery);

        //tickets by ticket ID(s)
        $this->filteredTicketQueryModifierForArrayFields('ticket-ids', 'tickets.id', $ticketsQuery);

        //tickets by status(es)
        $this->filteredTicketQueryModifierForArrayFields('status-ids', 'tickets.status', $ticketsQuery);

        //tickets owned by given user(s)
        $this->filteredTicketQueryModifierForArrayFields('owner-ids', 'tickets.user_id', $ticketsQuery);

        //tickets assigned to a given user(s)
        $this->filteredTicketQueryModifierForArrayFields('assignee-ids', 'assigned_to', $ticketsQuery);

        //tickets assigned to a given team(s)
        $this->filteredTicketQueryModifierForArrayFields('team-ids', 'team_id', $ticketsQuery);

        //ticket type is types of tickets. for eg. question
        $this->filteredTicketQueryModifierForArrayFields('type-ids', 'tickets.type', $ticketsQuery);

        //tickets created through given source(s). for eg. mail, web, facebook
        $this->filteredTicketQueryModifierForArrayFields('source-ids', 'tickets.source', $ticketsQuery);

        //tickets which are assigned or unassigned
        //TODO : create a seperate method for handling assigned to by team
        $this->filteredTicketQueryForBoolean('assigned', 'assigned_to', $ticketsQuery);

        //tickets which are answered or not answered
        $this->filteredTicketQueryForBoolean('answered', 'isanswered', $ticketsQuery);

        // if response sla is met
        $this->filteredTicketQueryForBoolean('has-response-sla-met', 'is_response_sla', $ticketsQuery);

        // if resolution sla is met
        $this->filteredTicketQueryForBoolean('has-resolution-sla-met', 'is_resolution_sla', $ticketsQuery);

        // if ticket is resolved
        $this->filteredTicketQueryForBoolean('is-resolved', 'closed', $ticketsQuery);

        //tickets which are answered or not answered
        $this->filteredTicketQueryForBoolean('reopened', 'reopened', $ticketsQuery);

        //tickets for given tags
        $this->filteredTicketsByLabelsAndTags('tag-ids', 'tag', $ticketsQuery);

        //tickets for given label
        $this->filteredTicketsByLabelsAndTags('label-ids', 'label', $ticketsQuery);

        //tickets due in a given time range
        $this->filteredTicketQueryModifierForTimeRange('due-on', 'duedate', $ticketsQuery);

        //tickets with created_at in a given time range
        $this->filteredTicketQueryModifierForTimeRange('created-at', 'tickets.created_at', $ticketsQuery);

        //tickets with updated_at in a given time range
        $this->filteredTicketQueryModifierForTimeRange('updated-at', 'tickets.updated_at', $ticketsQuery);

        //tickets with closed_at in a given time range
        $this->filteredTicketQueryModifierForTimeRange('closed-at', 'tickets.closed_at', $ticketsQuery);

        //tickets created by given user(s)
        $this->filteredTicketQueryModifierForArrayFields('creator-ids', 'tickets.creator_id', $ticketsQuery);

        //tickets created with location(s)
        $this->filteredTicketQueryModifierForArrayFields('location-ids', 'tickets.location_id', $ticketsQuery);

        // tickets created with a user with given organisation
        $this->filteredTicketQueryModifierForOrganizations($ticketsQuery);

        // filters resolution_time
        $this->filterTicketByTimeInterval('resolution-time', 'tickets.resolution_time', $ticketsQuery);

        // filters average response time
        $this->filterTicketByTimeInterval('avg-response-time', 'tickets.average_response_time', $ticketsQuery);

        // filters first response time
        $this->filterTicketByTimeInterval('first-response-time', 'ticket_thread.response_time', $ticketsQuery);

        //custom fields
        $this->searchByCustomField($ticketsQuery);

        //tickets which have given users as collaborator
        $this->filterTicketByRelation($ticketsQuery, 'collaborator-ids', 'collaborator', 'user_id');

        // apending extra parameters
        Event::dispatch('append-extra-filter-parameters', [$ticketsQuery, $this->request]);

        return $ticketsQuery;
    }

    /**
     * Gets the base query for tickets. This query can be appended with searchQuery to get desired result
     * @param bool $meta if relations data has to be appended to the query. USE CASE: we need relations only while seeing the tickets,
     *                    not while getting the ticket count
     * @return QueryBuilder
     * @throws Exception
     */
    public function baseQueryForTickets($meta = true) : QueryBuilder
    {
        $category = $this->request->input('category') ? $this->request->input('category') : 'all';

        $this->timezone = agentTimeZone();

        $ticketsQuery = $this->ticketsQueryByCategory($category);

        // if query is for counting number of records, then relations and search queries will not be required
        if($meta){
            $ticketsQuery = $this->appendForeignKeyQuery($ticketsQuery);
        }

        $ticketsQuery = $this->generalSearchQuery($ticketsQuery);

        //if search filter is required
        return $this->filteredTickets($ticketsQuery);
    }

    /**
     * Appends foreign query to base query
     * @param $ticketsQuery
     * @return mixed
     */
    protected function appendForeignKeyQuery($ticketsQuery)
    {
        $ticketsQuery->with([
            'user:id,user_name,first_name,last_name,profile_pic,email',
            'assigned:id,user_name,first_name,last_name,profile_pic,email',
            'firstThread:id,ticket_id,title',
            'priority:priority_id,priority as name,priority_color',
            'sources:id,name,css_class',
            'assignedTeam:id,name',
            'departments:id,name',
            'statuses:id,name,icon_color,icon',
            'creator:id,user_name,first_name,last_name,profile_pic,email',
            'location:id,title',
            'labels:labels.id,ticket_id,value,color'
        ]);

        //getting extra fields according to ticket settings
        return $this->getExtraFieldsIfRequired($ticketsQuery);
    }

    /**
     * Gets general search query. (this will only be used by 'baseQueryForTickets' method)
     * @param  QueryBuilder $baseQuery base query
     * @param string $searchString string which has to be searched
     * @return QueryBuilder
     */
    protected function generalSearchQuery(QueryBuilder $baseQuery) : QueryBuilder
    {
        $searchString = $this->request->input('search-query');

        if(!$searchString){
            return $baseQuery;
        }

        //search query for team has to be added too
        return $baseQuery->where(function ($q) use ($searchString) {
            $q->where('ticket_number', 'LIKE', "%$searchString%")
                ->orwhereHas('user', function ($query) use ($searchString) {
                    $this->appendUserSearchQuery($query, $searchString);
                })
                ->orWhereHas('assigned', function ($query) use ($searchString) {
                    $this->appendUserSearchQuery($query, $searchString);
                })
                ->orWhereHas('firstThread', function ($query) use ($searchString) {
                    $query->where('title', 'LIKE', "%$searchString%");
                });
        });
    }

    /**
     * Appends user search query to the parent query, given it is getting queried from user table
     * @param  QueryBuilder $parentQuery  query into which user search query has to be appended
     * @param  string       $searchString string that needs to be searched
     * @return null
     */
    public function appendUserSearchQuery(QueryBuilder &$parentQuery, string $searchString)
    {
        $parentQuery = $parentQuery->where('first_name', 'LIKE', "%$searchString%")
        ->orWhere('last_name', 'LIKE', "%$searchString%")
        ->orWhere('user_name', 'LIKE', "%$searchString%")
        ->orWhere(function ($subQuery) use ($searchString) {
            $subQuery->where('email', 'LIKE', "%$searchString%")->where('email', '!=', null);
        });
    }

    /**
     * check for the passed fieldName in request and appends it query to ticketsQuery from DB
     * NOTE: it is just a helper method for  filteredTickets method and should not be used by other methods
     * @param $fieldNameInRequest string => field name in the request coming from front end
     * @param $fieldNameInDB string => field name in the db by which we query
     * @param $ticketsQuery => it is the base query to which search queries has to be appended.
     *                       This is passed by reference, so at the end of the method it gets updated
     * @return object => query
     */
    private function filteredTicketQueryModifierForArrayFields($fieldNameInRequest, $fieldNameInDB, &$ticketsQuery)
    {
        if ($this->request->input($fieldNameInRequest)) {
            $queryIds = $this->request->input($fieldNameInRequest);
            $ticketsQuery = $ticketsQuery->whereIn($fieldNameInDB, $queryIds);
        }
    }

    /**
     * check for the passed fieldName in request and appends it query to ticketsQuery from DB
     * NOTE: it is just a helper method for  filteredTickets method and should not be used by other methods
     * NOTE: All datetime passed are assumed to be in agent's timezone
     *
     * @param string $parameterNameInRequest     Parameter name in the request
     * @param string $fieldNameInDB              Field name in the db by which we query
     * @param string $ticketsQuery               It is the base query to which search queries has to be appended.
     *                                           This is passed by reference, so at the end of the method it gets updated
     * @return object                            Query after filtering in timesatmp range
     */
    private function filteredTicketQueryModifierForTimeRange($parameterNameInRequest, $fieldNameInDB, &$ticketsQuery)
    {
      $timeRange = $this->request->input($parameterNameInRequest);

      if($timeRange){

        $formattedRange = $this->getTimeRangeObject($timeRange, $this->timezone);

        $ticketsQuery = $ticketsQuery->where($fieldNameInDB, '<=', $formattedRange->end)->where($fieldNameInDB, '>=', $formattedRange->start);
      }
    }

    /**
     * check for the passed fieldName in request and appends it query to ticketsQuery from DB
     * NOTE: it is just a helper method for  filteredTickets method and should not be used by other methods
     * @param string $fieldNameInRequest        Field name in the request coming from front-end
     * @param string $fieldNameInDB             Field name in the db
     * @param string $ticketsQuery              It is the base query to which search queries has to be appended.
     *                                          This is passed by reference, so at the end of the method it gets updated
     * @return
     */
    private function filteredTicketQueryForBoolean($fieldNameInRequest, $fieldNameInDB, &$ticketsQuery)
    {
        if ($this->request->input($fieldNameInRequest) == '0' || $this->request->input($fieldNameInRequest) == '1') {
            $value = $this->request->input($fieldNameInRequest); //will be 0 or 1

            //in case of unassigned we also need to check if team_id is null
            //TODO: remove this and use polymorphic relationship in ticket table to store both team_id and user_id in assigned to
            //now it modifies ticketsQuery by appending the condition that both teamId and
            if ($fieldNameInRequest == 'assigned') {
                $ticketsQuery = $value ? $ticketsQuery->where(function ($q) {
                    $q->where('assigned_to', '!=', null)->orWhere('team_id', '!=', null);
                }) :
                        $ticketsQuery->where('team_id', null)->where('assigned_to', null);
            } else {
                //only this part is required for this method to work. Above if part is a workaround until
                // we change the tickets table to use polymorphic relationship for assigned to
                $ticketsQuery = $value ? $ticketsQuery->where($fieldNameInDB, '!=', null)->where($fieldNameInDB, '!=', 0):
                        $ticketsQuery->where(function ($q) use ($fieldNameInDB) {
                            $q->where($fieldNameInDB, null)->orWhere($fieldNameInDB, 0);
                        });
            }
        }
    }

    /**
     * check for the passed fieldName in request and appends it query to ticketsQuery from DB
     * NOTE: it is just a helper method for  filteredTickets method and should not be used by other methods
     * @param $type => either its tag or label
     * @param $ticketsQuery => it is the base query to which search queries has to be appended.
     *                       This is passed by reference, so at the end of the method it gets updated
     * @return
     */
    private function filteredTicketsByLabelsAndTags($type, $fieldNameInDB, $ticketsQuery)
    {
        if ($this->request->input($type)) {
            //value is an array of tags/labels
            $value = $this->request->input($type);

            //NOTE: it is a temporary solution for tags/labels to work until DB changes are merged
            //find tag names in tag table
            if ($fieldNameInDB == 'tag') {
                $names = Tag::whereIn('id', $value)->select('name')->pluck('name')->toArray();
            }
            if ($fieldNameInDB == 'label') {
                $names = Label::whereIn('id', $value)->select('title')->pluck('title')->toArray();
            }

            $ticketIds = Filter::where('key', $fieldNameInDB)->whereIn('value', $names)->select('id', 'ticket_id')->pluck('ticket_id')->toArray();
            $ticketsQuery = $ticketsQuery->whereIn('tickets.id', $ticketIds);
        }
    }

    /**
     * Appends user search query to the parent query, given it is getting queried from user table
     * @param  QueryBuilder $parentQuery  query into which user search query has to be appended
     * @param  string       $searchString string that needs to be searched
     * @return null
     */
    public function filteredTicketQueryModifierForOrganizations(QueryBuilder &$parentQuery)
    {
        $orgIds = $this->request->input('organization-ids');
        
        if($orgIds){
            $parentQuery = $parentQuery->whereHas('user.organizations', function($q) use ($orgIds){
                $q->whereIn('organization.id', $orgIds);
            });    
        }
    }

    /**
     * Simply, formats unformatted ticket ( nothing fancy at all )
     * @param $tickets => tickets with raw relationships(unformatted)
     * @return object => formatted list of tickets with $limit parameter
     */
    private function formatTickets($tickets)
    {
        $agentTimeZone = $this->timezone;

        $tickets['tickets'] = [];
        foreach ($tickets['data'] as $ticket) {
            $ticket['from'] = $ticket['user'];
            $ticket['title'] = utfEncoding($ticket['first_thread']['title']);
            $ticket['status'] = $ticket['statuses'];
            $ticket['source'] = $ticket['sources'];
            $ticket['priority']['id'] = $ticket['priority']['priority_id'];
            $ticket['department'] = $ticket['departments'];
            if (!is_null($ticket['location_id'])) {
                $ticket['location']['name'] = $ticket['location']['title'];
                unset($ticket['location']['title']);
            }

            $ticket['is_overdue'] = Tickets::isOverdue($ticket['duedate']);

            $ticket['due_today'] = Tickets::isDueToday($ticket['duedate'], $agentTimeZone);
            unset($ticket['user'], $ticket['user_id'], $ticket['assigned_to'], $ticket['help_topic_id'], $ticket['dept_id'], $ticket['priority_id'], $ticket['first_thread'], $ticket['statuses'], $ticket['sources'], $ticket['priority']['priority_id'], $ticket['departments']);

            $this->formatExtraFields($ticket);

            array_push($tickets['tickets'], $ticket);
        }

        unset($tickets['data'], $tickets['first_page_url'], $tickets['last_page_url'], $tickets['next_page_url'], $tickets['prev_page_url'], $tickets['path'], $tickets['to'], $tickets['source']);
        return $tickets;
    }

    /**
     * Simply, formats unformatted ticket ( nothing fancy at all )
     * @param object $ticketsQuery it is the base query to which search queries has to be appended.
     *                       This is passed by reference, so at the end of the method it gets updated
     * @return object ticketsQuery appended with custom field search query
     */
    private function searchByCustomField(&$ticketsQuery)
    {
        // loop over all requests and append query for custom fields
        foreach ($this->request->all() as $key => $value) {
            if (strpos($key, 'custom_') !== false && $value) {
                $formFieldId = str_replace('custom_','', $key);

                // alias has to be unique while using joins. Using key will make alias unique for
                // multiple custom fields
                $ticketsQuery = $ticketsQuery->leftJoin("custom_form_value as $key", "$key.custom_id", '=', 'tickets.id')
                    ->where("$key.custom_type", 'App\Model\helpdesk\Ticket\Tickets')
                    ->where("$key.form_field_id", $formFieldId);

                // check if value has a comma and field is a chec
                // if it is a checkbox, convert passed string into array
                $type = FormField::whereId($formFieldId)->value('type');

                // exploding the values followed by a trim, if type is a checkbox
                $type == 'checkbox' && $value = array_map('trim', explode(',', $value));

                if($type == "text" || $type == "textarea"){
                    $ticketsQuery->where("$key.value", 'LIKE', "%$value%");
                } else {
                    // we are json encoding the value while storing, so it is required to json encode it
                    // before querying for exact match. The same is not required in a LIKE query, since
                    // we are only looking for substring in that string
                    $value = json_encode($value);
                    $ticketsQuery->where("$key.value", $value);
                }
            }
        }
    }


    /**
     * gets ticket numbers based on search string. Search string can be ticket subject or ticket number
     * @param  Request $request
     * @return Response
     */
    public function getSuggestionsByTicketSubjectOrNumber(Request $request)
    {
        $searchString = $request->input('search-query') ? $request->input('search-query') : '';
        $limit = $request->input('limit') ? $request->input('limit') : 10;
        $category = $request->input('category') ? $request->input('category') : 'all';
        $ticketsQuery = $this->ticketsQueryByCategory($category);

        $tickets = $ticketsQuery->with('firstThread:id,title,ticket_id')
                ->where(function ($q) use ($searchString) {
                    $q->whereHas('firstThread', function ($q) use ($searchString) {
                        $q->where('title', 'LIKE', "%$searchString%");
                    })->orWhere('ticket_number', 'LIKE', "%$searchString%");
                })->select('id', 'ticket_number')
                ->orderBy('updated_at', 'DESC')
                ->take($limit)->get();

        //formatting tickets
        $formattedTickets = [];
        $i = 0;
        foreach ($tickets as $ticket) {
            $formattedTickets[$i]['id'] = $ticket['id'];
            $formattedTickets[$i]['name'] = $ticket['firstThread']['title'] . ' (' . $ticket['ticket_number'] . ')';
            $i = $i + 1;
        }

        return successResponse('', ['tickets' => $formattedTickets]);
    }


    /**
     * Gets Ticket List by filter Id
     * @param  int  $filterId
     * @return Response
     */
    private function getTicketListByFilterId(int $filterId)
    {
        if (!TicketFilter::isGivenFilterAccessible($filterId)) {
            return errorResponse('Invalid filter', 404);
        }

        $parameters = TicketFilter::getFilterParametersByFilterId($filterId);

        $this->request = $this->request ?: new Request;
        /**
         * fetching old General parameters from request to be passed in new
         * filtered request so that result can be updated for page, limit, etc.
         */
        $oldParameters = $this->request->only(['search-query', 'limit', 'page', 'sort-field', 'sort-order']);

        $this->request->replace(array_merge($parameters, $oldParameters));

        return $this->getTicketsList($this->request);
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

        return $this->baseQueryForTickets(false)->count();
    }

    /**
     * @param string $fieldNameInRequest
     * @param string $fieldNameInDb
     * @param QueryBuilder $ticketsQuery
     * @return QueryBuilder
     * @throws Exception
     */
    private function filterTicketByTimeInterval($fieldNameInRequest, $fieldNameInDb, &$ticketsQuery)
    {
        $valueInRequest = $this->request->input($fieldNameInRequest);

        if(!$valueInRequest) {
            return $ticketsQuery;
        }

        $timeObject = $this->getTimeIntervalInMinutes($valueInRequest);

        if($fieldNameInDb == 'ticket_thread.response_time'){

            $ticketsQuery = $ticketsQuery->whereHas('thread', function($q) use($fieldNameInDb, $timeObject) {
                $q->where('thread_type', 'first_reply')
                    ->where('is_internal', 0)
                    ->where('poster', 'support')
                    ->where($fieldNameInDb, "<=", $timeObject->end);

                if($timeObject->start){
                    $q->where($fieldNameInDb, ">", $timeObject->start);
                }
            });

        } else {
            $ticketsQuery = $ticketsQuery->where($fieldNameInDb, "<=", $timeObject->end);

            if($timeObject->start){
                $ticketsQuery = $ticketsQuery->where($fieldNameInDb, ">", $timeObject->start);
            }
        }

        return $ticketsQuery;
    }

    /**
     * This API can be used by all the data-tables which needs tickets
     * @param Request $request
     * @return \HTTP
     */
    public function getTicketCountForCategories(Request $request)
    {
        $this->request = $request;
        $categories = ["open"=> "inbox", "closed"=> "closed", "unapproved"=> "unapproved", "deleted" => "deleted"];
        $responseArray = [];
        foreach ($categories as $categoryName => $categoryKey) {
            $this->request->request->set('category', $categoryKey);
            $responseArray[$categoryName] = $this->getTicketCountByParameters($this->request->all());
        }
        return successResponse("", $responseArray);
    }

    /**
     * Function to format query builder for filter results by relationships
     * NOTE: it is just a helper method for filteredTickets method and can not be used by other methods
     * @param $ticketsQuery => it is the base query to which search queries has to be appended.
     *                       This is passed by reference, so at the end of the method it gets updated
     * @param $fieldNameInRequest string => field name in the request coming from front end
     * @param $releation string => relation name in the ticket model
     * @param string $relation(name of relation),
     * @param string $column(name of column to use in where condition)
     *
     * @return void
     */
    protected function filterTicketByRelation(QueryBuilder &$ticketsQuery, string $fieldNameInRequest, string $releation, string $column="id"):void
    {
        if ($this->request->input($fieldNameInRequest)) {
            $value = $this->request->input($fieldNameInRequest, []);
            $ticketsQuery =  $ticketsQuery->whereHas($releation, function ($query) use ($column, $value) {
                $query->whereIn($column, $value);
            });
        }
    }
}