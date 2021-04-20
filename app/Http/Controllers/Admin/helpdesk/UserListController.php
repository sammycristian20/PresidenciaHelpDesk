<?php

namespace App\Http\Controllers\Admin\helpdesk;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Agent\Teams;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\User;
use Auth;

/**
 * Handles users list view by filtering/searching/arranging users 
 * USAGE :
 * Request can have following parameters:
 * 
 *               NAME          |        Possible values 
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
 * active (array, optional)
 * roles (array, optional)
 * deactive(array, optional)
 * dept-ids (array, optional)
 * team-ids (array, optional)
 * org-ids (array, optional)
 * @author krishna vishwakarma <krishna.vishwakarma@ladybirdweb.com>
 */

class UserListController extends Controller
{
    protected $request;

    public function __construct() {
        $this->middleware(['auth', 'role.agent']);
    }

    /**
     * Gets users-list base
     * @return array => success response with filtered users
     */
    public function getUserList(Request $request)
    {
        $this->request = $request;
        $searchString = $request->input('search-query') ? $request->input('search-query') : '';
        $limit = $request->input('limit') ? (int)$request->input('limit') : 10;
        $sortField = $request->input('sort-field') ? $request->input('sort-field') : 'updated_at';
        $sortField = $sortField == 'name' ? 'first_name' : $sortField;
        $sortOrder = $request->input('sort-order') ? $request->input('sort-order') : 'desc'; 
        $usersQuery = User:: with(['organizations:organization.id,name'])
                    ->where('is_delete', 0)
                    ->select('id', 'first_name', 'last_name', 'role', 'user_name', 'email', 'phone_number', 'mobile', 
                      'active','email_verify','mobile_verify', 'is_delete', 'last_login_at', 'is_2fa_enabled')
                      ->orderBy($sortField, $sortOrder);


        $baseQueryWithoutSearch = $this->filteredUsers($usersQuery);
        $baseQuery = $searchString ? $this->generalSearchQuery($baseQueryWithoutSearch, $searchString) : $baseQueryWithoutSearch;
        $users = $baseQuery->paginate($limit);
        $formattedUsers = $this->formatUsers($users->toArray());
        
        return successResponse('',$formattedUsers); 
    }

   /**
     * Simply, formats unformatted users
     * @param $users => users with extra fields
     * @return array => formatted list of users with limited fields
     */
    private function formatUsers($users)
    {
        foreach ($users['data'] as &$user) {
            if (is_numeric($user['phone_number'])) {
                $user['mobile'] = $user['mobile'] == 'Not available' ? '' : $user['mobile'];
            }
            $user['phone'] = $user['phone_number'] . ' ' . $user['mobile'];
            $user['last_login_at'] = $user['last_login_at'];
            $user['name'] = $user['full_name'];
            unset($user['first_name'], $user['last_name'], $user['phone_number'], $user['mobile'], $user['full_name']);
        }
        
        $users['users'] = $users['data'];
        unset($users['data']);

        return $users;
    }

    /**
     * Takes users's base query and appends to it search query according to whether that search parameter is present in the request or not
     * @param $userssQuery
     * @return object => query
     */
    private function filteredUsers(QueryBuilder $usersQuery) : QueryBuilder
    {
        //active/verified or unverified users (while account activaton active and email_verify is set to 1)
        $this->viewByFieldValueTypeUserQuery('active', 'active', $usersQuery);

        //users based on roles
        $this->viewByFieldValueTypeUserQuery('roles', 'role', $usersQuery);

        //deactivated users
        $this->viewByFieldValueTypeUserQuery('deactive', 'active', $usersQuery);

        //users in a given department(s)
        $this->filteredUserQueryModifierForDepartmentsArrayFields('dept-ids', 'department.id', $usersQuery);
        
        //users in a given team(s)
        $this->filteredUserQueryModifierForTeamsArrayFields('team-ids', 'teams.id', $usersQuery);

        //users in a given organization(s)
        $this->filteredUserQueryModifierForOrganizationsArrayFields('org-ids', 'organization.id', $usersQuery);

        return $usersQuery;
    }

    /**
     * check for the passed fieldName in request and appends it query to usersQuery from DB
     * NOTE: it is just a helper method for  filteredUsers method and should not be used by other methods
     * @param $fieldNameInRequest string => field name in the request coming from front end
     * @param $fieldNameInDB string => field name in the db by which we query
     * @param &$usersQuery => it is the base query to which search queries has to be appended.
     *                       This is passed by reference, so at the end of the method it gets updated
     * @return object => query
     */
    private function filteredUserQueryModifierForDepartmentsArrayFields($fieldNameInRequest, $fieldNameInDB, &$usersQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $queryIds = (array) $this->request->input($fieldNameInRequest);

            $usersQuery = $usersQuery->WhereHas('departments', function($q) use($fieldNameInDB, $queryIds) {
                            $q->whereIn($fieldNameInDB, $queryIds);
                          });
        }
    }

    /**
     * check for the passed fieldName in request and appends it query to usersQuery from DB
     * NOTE: it is just a helper method for  filteredUsers method and should not be used by other methods
     * @param $fieldNameInRequest string => field name in the request coming from front end
     * @param $fieldNameInDB string => field name in the db by which we query
     * @param &$usersQuery => it is the base query to which search queries has to be appended.
     *                       This is passed by reference, so at the end of the method it gets updated
     * @return object => query
     */
    private function filteredUserQueryModifierForOrganizationsArrayFields($fieldNameInRequest, $fieldNameInDB, &$usersQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $queryIds = (array) $this->request->input($fieldNameInRequest);

            $usersQuery = $usersQuery->WhereHas('organizations', function($q) use($fieldNameInDB, $queryIds) {
                            $q->whereIn($fieldNameInDB, $queryIds);
                          });
        }
    }

    /**
     * check for the passed fieldName in request and appends it query to usersQuery from DB
     * NOTE: it is just a helper method for  filteredUsers method and should not be used by other methods
     * @param $fieldNameInRequest string => field name in the request coming from front end
     * @param $fieldNameInDB string => field name in the db by which we query
     * @param &$usersQuery => it is the base query to which search queries has to be appended.
     *                       This is passed by reference, so at the end of the method it gets updated
     * @return object => query
     */
    private function filteredUserQueryModifierForTeamsArrayFields($fieldNameInRequest, $fieldNameInDB, &$usersQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $queryIds = (array) $this->request->input($fieldNameInRequest);
            $usersQuery = $usersQuery->WhereHas('teams', function($q) use($fieldNameInDB, $queryIds) {
                            $q
                                ->whereIn($fieldNameInDB, $queryIds)
                                ->where('status', 1);
                          });
        }
    }

    /**
     * check for the passed fieldName in request and appends it query to usersQuery from DB
     * NOTE: it is just a helper method for  filteredUsers method and should not be used by other methods
     * @param $fieldNameInRequest string => field name in the request coming from front end
     * @param $fieldNameInDB string => field name in the db by which we query
     * @param $usersQuery => it is the base query to which search queries has to be appended.
     *                       This is passed by reference, so at the end of the method it gets updated
     * @return object => query
     */
    private function viewByFieldValueTypeUserQuery($fieldNameInRequest, $fieldNameInDB, &$usersQuery)
    {
        if ($this->request->has($fieldNameInRequest)) {
            $queryIds = (array) $this->request->input($fieldNameInRequest);
            $usersQuery = $usersQuery->whereIn($fieldNameInDB, $queryIds);
        }
    }

    /**
     * Gets general search query. (this will only be used by 'baseQueryForUsers' method)
     * @param  QueryBuilder $baseQuery base query
     * @param string $searchString string which has to be searched
     * @return QueryBuilder
     */
    private function generalSearchQuery(QueryBuilder $baseQuery, string $searchString) : QueryBuilder
    { 
        return $baseQuery->where(function($q) use ($searchString) {
                $q
                    ->where('first_name', 'LIKE', "%$searchString%")
                    ->orWhere('last_name', 'LIKE', "%$searchString%")
                    ->orWhere('user_name', 'LIKE', "%$searchString%")
                    ->orwhere('email', 'LIKE', "%$searchString%")
                    ->orWhere('mobile', 'LIKE', "%$searchString%")
                    ->orWhere('phone_number', 'LIKE', "%$searchString%")
                    ->orWhereRaw("concat(first_name, ' ', last_name) LIKE ?", ['%'.$searchString.'%']);
        });
    }
}