<?php
namespace App\Http\Controllers\Common\Dependency;

use App\Exceptions\DependencyNotFoundException;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Manage\Help_topic as HelpTopic;
use App\Model\helpdesk\Manage\Tickettype as TicketType;
use App\Model\helpdesk\Manage\UserType;
use App\Model\helpdesk\Ticket\Ticket_Priority as TicketPriority;
use App\Model\helpdesk\Ticket\Ticket_source as TicketSource;
use App\Model\helpdesk\Ticket\Ticket_Status as TicketStatus;
use App\Model\helpdesk\Utility\CountryCode;
use App\Location\Models\Location;
use App\Repositories\FormRepository;
use Auth;
use App\Model\helpdesk\Ratings\Rating;
use App\Model\helpdesk\Agent\Department;
use Config;
use DB;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Lang;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Utility\Timezones;
use App\Traits\KbDependency;
use App\Model\helpdesk\Ticket\TicketStatusType;
use App\Http\Controllers\Client\helpdesk\ClientTicketListController;
use App\User;

/**
 * handles auto suggestions/dependency list  of fields(like helpTopic, priorities, statuses etc.) throughout the project
 *
 * USAGE:
 * A request sent to method 'handle' can have four parameters
 *
 *  1. search-query (string, optional) => The string that is required to be searched.If it is not passed in the parameter,
 *                                        all the records (maximum 10, if limit parameter is also not passed) will be returned.
 *  2. limit (integer, optional)       => Number of records that is needed to be fetched from DB. If it is not passed,
 *                                        first 10 records will be returned.
 *  3. meta (boolean, optional)        => In some scenario, we need more information from a table then just ‘id’ and ‘name’.
 *                                        In that case *meta* will be passed as true.For eg. In case of user, at some places
 *                                        we only need just id and name, but at some places we need profile_pic and email also.
 *                                        So, for getting more detailed information meta must be passed as true.
 *                                          Possible use cases:
 *                                              1. users (email, profile_pic)
 *                                              2. statuses (icon, icon_class)
 *                                              3. priority (priority_color)
 *                                              4. agents (email, profile_pic)
 *                                              5. types (type_desc)
 *                                              6. sources (css_class)
 *
 *                                        Fields in bracket are the extra fields which will be returned in the response
 *                                        when meta is passed as true.
 *
 *  4. config (boolean, optional)       => In admin panel, certain fields (such as status) are configured so, for that purpose
 *                                         all the fields are required irrespective of that field is private/public or active/inactive.
 *                                         But in agent panel, only those fields are required which are activated through admin panel.
 *                                         So, if this parameter is passed as true, backend will send all the rows and columns available
 *                                         in the table
 *
 *
 * Now, these four parameter can/cannot be passed. So for that method *initializeParameterValues()* initilises those values for the other
 * class methods to work.
 *
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class DependencyController extends NonPublicDependencies
{
    use KbDependency;

    /**
     * Gets list of elements according to the passed Type.
     * @param string $type dependency type (like help-topics, priorities etc)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle($type, Request $request)
    {
        try {
            //populating parameter variables to handle addition params in the request . For eg. search-query, limit, meta, config
            $this->initializeParameterValues($request);

            /*
             * Once class variables like config, meta, limit, search-query, userRole is populated, it can be used throughout the class
             * to give user relevant information according to the paramters passed and userType
             */
            $data = $this->handleDependencies($type);

            if (!$data) {
                return errorResponse(Lang::get('lang.fails'));
            }

            return successResponse('', $data);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Gets dependency data according to the value of $type
     * @param string $type Dependency type (like help-topics, priorities etc)
     * @return array|boolean    Array of dependency data on success, false on failure
     * @throws DependencyNotFoundException
     */
    protected function handleDependencies($type)
    {
        $this->dependencyKey = $type;

        switch ($type) {
            case 'help-topics':
                return $this->helpTopics();

            case 'departments':
                return $this->departments();

            case 'priorities':
                return $this->priorities();

            case 'user-types':
                return $this->userTypes();

            case 'types':
                return $this->types();

            case 'sources':
                return $this->sources();

            case 'statuses':
                return $this->statuses();

            case 'languages':
                return $this->languages();

            case 'rating-types':
                return $this->ratingTypes();

            case 'country-codes':
                return $this->countryCodes();

            case 'locations':
                return $this->locations();

            case 'organizations':
                return $this->organizations();

            case 'time-zones':
                return $this->timeZones();

            case 'plugins':
                return $this->plugins();

            case 'categories':
                return $this->categories();

            case 'article-templates':
                return $this->articleTemplates();

            case 'ticket-status-tabs':
                return $this->clientPanelTicketStatusTabList();

            default:
                return $this->handleNonPublicDependencies($type);
        }
    }
    /**
     * Gets list of helpTopic with necessary fields.
     * @return array    list of helptopics
     */
    protected function helpTopics()
    {
        $baseQuery = $this->baseQuery(new HelpTopic)->where('topic', 'LIKE', "%$this->searchQuery%")
            ->select('id', 'topic as name');

        // if supplements is not empty, then get helptopics linked with passed department
        if ($this->request->input('department_id')) {
            $baseQuery = $this->limitLinkedHelpTopics($baseQuery, (array)$this->request->input('department_id'));
        }

        if (!$this->config) {
            $baseQuery->where('status', 1);
        }

        //if user is logged in as client or not logged in at all
        if ($this->userRole == 'user') {
            //type 1 means public, 0 means private
            $baseQuery = $baseQuery->where('type', 1);
        }
        // pass 'panel' parameter from frontend
        // for client panel 'client'
        // for agent panel 'agent'
        $this->meta && $this->appendFormFieldQuery($baseQuery, $this->request->input('scenario'), $this->request->panel);

        return $this->get('help_topics', $baseQuery, function ($element) {
            $this->meta && FormRepository::formatFormElements($element, $this->config);
            return $element;
        });
    }

    /**
     * Gets list of priority with necessary fields.
     * @return array    list of users
     */
    protected function priorities()
    {
        $baseQuery = $this->baseQuery(new TicketPriority)->where('priority', 'LIKE', "%$this->searchQuery%")
            ->select('priority_id as id', 'priority as name');

        if (!$this->config) {
            $baseQuery->where('status', 1);
        }

        if ($this->meta) {
            $baseQuery->addSelect('priority_color');
        }

        if ($this->userRole == 'user') {
            $baseQuery = $baseQuery->where('ispublic', 1);
        }

        return $this->get('priorities', $baseQuery);
    }


    /**
     * Gets list of users with necessary fields.
     * @return array    list of users
     */
    protected function users()
    {
        $roles = ['agent', 'admin', 'user'];
        if ($this->supplements == 'user-only') {
            $roles = ['user'];
        }
        if ($this->supplements == 'agent-admin-only') {
            $roles = ['agent', 'admin'];
        }
        $baseQuery = $this->getBaseQueryForUserByRole($roles);

        return $this->get('users', $baseQuery, function ($element) {
            $organizationDetails = implode(',', array_column($element->organizations->toArray(), "name"));

            $element->name = ($organizationDetails && $this->meta)
                ? $element->meta_name. " <" . trans('lang.organizations'). ": $organizationDetails>"
                : $element->meta_name;

            unset($element->meta_name);
            return $element;
        });
    }

    /**
     * Available user types
     */
    protected function userTypes()
    {
        $baseQuery = $this->baseQuery(new UserType)->where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->meta) {
            $keys = ['department_manager', 'team_lead', 'organization_manager'];
            (isMicroOrg()) ? $keys = array_merge($keys, ['organization_department_manager']) : '';
            $baseQuery = $baseQuery->whereIn('key', $keys)->select('id', 'name');
        }

        return $this->get('user_types', $baseQuery);
    }

    /**
     * Gets list of ticket types
     * @return array    list of available ticket types
     */
    protected function types()
    {
        $baseQuery = $this->baseQuery(new TicketType)->where('name', 'LIKE', "%$this->searchQuery%");

        !$this->config && $baseQuery->where('status', 1)->select('id', 'name');

        $this->meta && $baseQuery->addSelect('type_desc');

        $this->userRole === 'user' && $baseQuery = $baseQuery->where('ispublic', 1);

        $this->request->input('help_topic_id') && $this->limitLinkedTypes($baseQuery, (array)$this->request->input('help_topic_id'));

        return $this->get('types', $baseQuery);
    }

    /**
     * Gets list of sources by which a ticket can be created.
     * @return array list of available ticket sources
     */
    protected function sources()
    {
        $this->sortField = 'name';
        $this->sortOrder = 'asc';

        $baseQuery = $this->baseQuery(new TicketSource)->where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        if ($this->meta) {
            $baseQuery->addSelect('value')->addSelect('css_class');
        }

        return $this->get('sources', $baseQuery);
    }

    /**
     * Gets list of people and teams to whom a ticket can be assigned with necessary fields.
     * @return array list of statuses
     */
    protected function statuses()
    {
        $this->sortField = 'order';

        $baseQuery = (!empty($this->supplements)) ?
        $this->getOverrideStatuses($this->supplements, $this->searchQuery) :
        $this->baseQuery(new TicketStatus)->where('name', 'LIKE', "%$this->searchQuery%");

        \Event::dispatch('dependency-statuses-query-build', [$baseQuery, $this->supplements]);

        if (!$this->config) {
            // approval(5),unapproved(7),merged(8) status has to be skipped
            $baseQuery = $baseQuery->whereNotIn('purpose_of_status', [5,7,8])->select('id', 'name');
        }

        if ($this->userRole == 'user') {
            $baseQuery = $baseQuery->where('visibility_for_client', 1);
        }

        if ($this->userRole == 'agent') {
            // $baseQuery = $baseQuery->where('visibility_for_agent', 1); //column visibility_for_agent is not used

            //if delete permission is not granted to an agent, he should won't be seeing deleted status (purpose_of_status is 4)
            $baseQuery    = !User::has('delete_ticket') ? $baseQuery->where('purpose_of_status', '!=', 4) : $baseQuery;
        }

        // to get statuses based on purpose_of_status
        $baseQuery = $baseQuery->when(array_key_exists('purpose_of_status', $this->supplements), function($query) {
            $query->whereIn('purpose_of_status', $this->supplements['purpose_of_status'])->addSelect('purpose_of_status');
        });
        
        if ($this->meta) {
            $baseQuery->addSelect('icon_color')->addSelect('purpose_of_status')->addSelect('icon')->addSelect('allow_client')->addSelect('comment');
        }
        return $this->get('statuses', $baseQuery);
    }

    /**
      * Gets list of languages availble in faveo
      * @return array list of languages
      */
    protected function languages()
    {
        try {
            $appPath      = base_path();
            $languageList = array_map('basename', \File::directories("$appPath/resources/lang"));
            $languages    = [];

            foreach ($languageList as $key => $langLocale) {
                $language       = [];
                $language['id'] = $key;
                $languageArray  = Config::get("languages.$langLocale");
                if ($this->meta) {
                    $language['locale']      = $langLocale;
                    $language['translation'] = $languageArray[1];
                    //TODO: direction must be moved to a configuration file
                    $language['direction'] = $langLocale == 'ar' ? 'rtl' : 'ltr';
                }
                $language['name'] = $languageArray[0];
                array_push($languages, $language);
            }
            return ['languages' => $languages];
        } catch (\Exception $e) {
            return exceptionResponse($e);
        }
    }

    /**
     * gets the rating types from the DB
     * @return array        array of available rating types
     */
    protected function ratingTypes()
    {
        $this->sortField = 'display_order';
        $this->sortOrder = 'asc';

        $baseQuery = $this->baseQuery(new Rating)->where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery->addSelect('id')->addSelect('name');
        }

        if ($this->meta) {
            $baseQuery->addSelect('rating_scale')->addSelect('rating_area')->addSelect('allow_modification')->addSelect('restrict');
        }

        return $this->get('rating_types', $baseQuery);
    }

    /**
     * gives array of country codes
     * @return array            array of country codes
     */
    protected function countryCodes()
    {
        $this->sortField = 'name';

        $this->sortOrder = 'asc';

        $baseQuery = $this->baseQuery(new CountryCode)->where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery->addSelect('id')->addSelect('nicename as name');
        }

        if ($this->meta) {
            $baseQuery->addSelect('iso')->addSelect('phonecode')->addSelect('example');
        }

        return $this->get('country_codes', $baseQuery);
    }

    /**
    * gives array of Organization
    * @return array            array of organizations
    */
    protected function organizations()
    {
        $baseQuery = $this->baseQuery(new Organization)->where('name', 'LIKE', "%$this->searchQuery%");

        if (!$this->config) {
            $baseQuery = $baseQuery->select('id', 'name');
        }

        return $this->get('organizations', $baseQuery);
    }

    /**
    * gives array of locations
    * @return array            array of locations
    */
    protected function locations()
    {
        $baseQuery = $this->baseQuery(new Location)->where('title', 'LIKE', "%$this->searchQuery%")
            ->select('id', 'title as name');

        return $this->get('locations', $baseQuery);
    }

    /**
    * gives array of time zones
    * @return array            array of time zones
    */
    protected function timeZones()
    {
        $this->sortField = 'name';
        $this->sortOrder = 'asc';

        $baseQuery = $this->baseQuery(new Timezones)
            ->whereRaw("concat(location, ' ', name) LIKE ?", ['%'. $this->searchQuery .'%'])
            ->select('id', 'name', 'location');


        return $this->get('time_zones', $baseQuery, function ($element) {
            return (object)['id'=>$element->id, 'name'=> $element->timezone_name];
        });
    }

    /**
     * Gets list of departments
     * @return array list of departments
     */
    protected function departments()
    {
        $baseQuery = $this->baseQuery(new Department)->where('name', 'LIKE', "%$this->searchQuery%");

        $baseQuery = $baseQuery->orderBy('name')->select('id', 'name');

        // if supplements is not empty, then get helptopics linked with passed department
        if ($this->request->input('help_topic_id')) {
            $baseQuery = $this->limitLinkedDepartments($baseQuery, (array)$this->request->input('help_topic_id'));
        }

        if ($this->userRole == 'user') {
            $baseQuery->where('type', 1);
        }

        // pass 'panel' parameter from frontend
        // for client panel 'client'
        // for agent panel 'agent'
        $this->meta && $this->appendFormFieldQuery($baseQuery, $this->request->input('scenario'), $this->request->panel);

        return $this->get('departments', $baseQuery, function ($element) {
            $this->meta && FormRepository::formatFormElements($element, $this->config);
            return $element;
        });
    }

    /**
     * Get List of all the  plugins
     */
    public function plugins()
    {
        $baseQuery = \DB::table('plugins')->select('id', 'name');

        if (!$this->meta) {
            $baseQuery = $baseQuery->where('status', 1);
        }

        return $this->get('active_plugins', $baseQuery);
    }

    /**
     * method to get client panel ticket status tab list
     */
    public function clientPanelTicketStatusTabList()
    {
        $this->sortField = 'order';

        if (Auth::check()) {
            $baseQuery = $this->baseQuery(new TicketStatus)
                ->select('id', 'name', 'icon')
                ->where([['visibility_for_client', 1], ['name', 'LIKE', "%$this->searchQuery%"], ['secondary_status', null]])
                ->with(['alternativeStatus:id,secondary_status'])
                ->orderBy('order');

            $organization = $this->formatOrganizationIdsToMakeItUsableInApiEndPoint();

            return $this->get('status_tab_list', $baseQuery, function ($element) use ($organization) {
                $this->formatClientPanelTicketStatusTab($element, $organization);
                return $element;
            });
        }
        throw new UnauthorizedException('Access denied');
    }

    /**
     * method to format client panel ticket status tab list
     * @param $statusTab
     * @param $organization
     * @return NULL
     */
    private function formatClientPanelTicketStatusTab(&$statusTab, $organization)
    {
        $apiEndPoint = 'api/client/ticket-list?status-ids[]=' . $statusTab->id;
        $ticketStatusIds = [$statusTab->id];
        foreach ($statusTab->alternativeStatus as $alternativeStatus) {
            array_push($ticketStatusIds, $alternativeStatus->id);
            $apiEndPoint = $apiEndPoint . '&status-ids[]=' . $alternativeStatus->id;
        }
        $statusTab->api_end_point = $apiEndPoint;
        $parameters = ['status-ids' => $ticketStatusIds];
        if ($organization['organization_ids']) {
            $statusTab->api_end_point = $statusTab->api_end_point . $organization['parameter_set'];
            $parameters = array_merge($parameters, ['organization-ids' => $organization['organization_ids']]);
        }
        $statusTab->tickets_count = (new ClientTicketListController())->getTicketCountByParameters($parameters);
        $statusTab->unsetRelation('alternativeStatus');
    }

    /**
     * method to format organization ids and make it in form of api parameters
     * so that it could be used in api end point
     * @return array/NULL $organization
     */
    private function formatOrganizationIdsToMakeItUsableInApiEndPoint()
    {
        // supplements key could be used to pass organization ids explicitly based on that ticket count and ticket list response is altered
        $organization['organization_ids'] = array_filter((array) $this->supplements);
        $organization['parameter_set'] = '';
        foreach ($organization['organization_ids'] as $organizationId) {
            $organization['parameter_set'] = implode('', [$organization['parameter_set'], '&organization-ids[]=', $organizationId]);
        }
    
        return $organization;
    }
}
