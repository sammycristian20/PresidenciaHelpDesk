<?php


namespace App\Http\Controllers\Common\Dependency;

use App\Http\Controllers\Controller;
use App\Repositories\FormRepository;
use Auth;
use Closure;

class BaseDependencyController extends Controller
{
    /**
     * @var string      Search String that is required to be searched
     */
    protected $searchQuery;

    /**
     * if search should match the exact searchQuery
     * @var bool
     */
    protected $strictSearch = false;

    /**
     * @var integer     Maximum number of rows that are required
     */
    protected $limit;

    /**
     * What should be the order of the result
     * @var string
     */
    protected $sortOrder;

    /**
     * The field which has to be sorted
     * @var string
     */
    protected $sortField;

    /**
     * @var string      Role of the user (admin, agent, user)
     *                  It should be initialized in a way that if user is not logged in, its value should be 'user'
     */
    protected $userRole;

    /**
     * @var boolean     In most of the cases only few columns like 'id' and 'name' is required in the response,
     *                  but when more information is required other than 'id' and 'name', meta must be initialized as true
     *                  for eg. in case of ticket status, sometimes 'id' and 'name' of the status is enough but sometimes we need 'icon' and 'icon_color' also.
     *                  In that case $meta must be initialized as true, else false
     */
    protected $meta;

    /**
     * @var boolean     There are certain differences in data that is required in agent panel (for display purpose)  and in admin panel (for config purpose).
     *                  For eg. In ticket status, status like 'unapproved' is not required in agent panel but required in admin panel irrespective of
     *                  user being an admin. In that case $config must initialized as true, else false.
     *                  Also, when meta is passed as true, all the fields in the DB will be returned irrespective of value of $meta
     *                  because it is required in admin panel for configuration purpose
     */
    protected $config;

    /**
     *@var array        When we need to call the methods avaible in EnhancedDependency trait to update or modify list data based on specail conditions
     *
     */
    protected $supplements;

    /**
     * the ids the dependencies that we need to fetch
     * @var array
     */
    protected $ids = [];

    /**
     * Dependency key which will be used to see which dependency to call
     * @var string
     */
    protected $dependencyKey;

    /**
     * If passed as true, it will paginate the dependency
     * @var bool
     */
    protected $paginate = false;

    /**
     * Reauest
     * @var \Request
     */
    protected $request;

    /**
     * Populates class variables to handle addition params in the request . For eg. search-query, limit, meta, config, so that
     * it can be used throughout the class to give user relevant information according to the parameters passed and userType
     * @param object $request
     * @return
     */
    protected function initializeParameterValues($request)
    {
        $this->request = $request;

        $this->searchQuery = $request->input('search-query') ?: '';

        $this->limit = $request->input('limit') ?: 10;

        if ($request->input('limit') == 'all') {
            // making it a big number on the assumption that number of records will be
            // less than 10000. In case of dependencies, this limit is legit
            $this->limit = 10000;
        }

        $this->userRole = Auth::check() ? Auth::user()->role : 'user';

        $this->ids = $request->input('ids') ?: [];

        //only admin can set config as true
        // or can be set through code when $ids are non empty
        $this->config = ($this->userRole == 'admin' || count($this->ids)) ? (bool)$request->input('config') : false;


        //Config will be true if it is accessed from admin panel, in that case all the data & columns in the table will be returned
        //So meta don't have to be true
        $this->meta        = $request->input('meta') ? : false;

        $this->supplements = $request->input('supplements') ?: [];

        $this->sortField = $request->input('sort-field') ?: 'name';

        $this->sortOrder = $request->input('sort-order') ?: 'asc';

        $this->strictSearch = (bool) $request->input('strict-search');

        $this->paginate = (bool) $request->input('paginate');
    }

    /**
     * Base query builder.
     * @internal useful in case where we want a single dependency element to be extracted based on ID
     * @param $model
     * @return mixed
     */
    protected function baseQuery($model)
    {
        $baseQuery = $model->query();

        /*
         * We do not have proper DB structure for all dependencies, so some workarounds are added
         */
        if (count($this->ids)) {
            $primaryKey = ($this->dependencyKey == 'priorities') ? 'priority_id' : 'id';
            $baseQuery = $baseQuery->whereIn($model->getTable().'.'.$primaryKey, $this->ids);
        }

        // if strict search is on, it should not give any other result but just the search query exact match
        if ($this->strictSearch) {
            $searchKey = ($this->dependencyKey == 'help-topics') ? 'topic' : 'name';
            $baseQuery = $baseQuery->where($model->getTable().'.'.$searchKey, $this->searchQuery);
        }

        return $baseQuery;
    }

    /**
     * Gets dependency record in required format
     * @param $dependencyName
     * @param $baseQuery
     * @param Closure|null $callback
     * @return array
     */
    protected function get($dependencyName, $baseQuery, Closure $callback = null)
    {

        if ($this->config) {
            $baseQuery->addSelect($baseQuery->getModel()->getTable().'.*');
        }

        if ($this->sortField && $this->sortOrder) {
            $baseQuery->orderBy($this->sortField, $this->sortOrder);
        }

        if ($this->paginate) {
            $result = $baseQuery->simplePaginate($this->limit);

            if ($callback) {
                $result->getCollection()->transform($callback);
            }
            return $result;
        }

        $result = $baseQuery->take($this->limit)->get();
        if ($callback) {
            $result = $result->transform($callback);
        }

        return [$dependencyName => $result];
    }


    /**
     * Appends form field queries based on the if it is getting used for configuration OR rendering
     * @param $baseQuery
     * @param string $scenario
     * @param string $panel
     * @internal it is a helper method for dependencies with child fields
     */
    protected function appendFormFieldQuery(&$baseQuery, $scenario = 'create', $panel = 'agent')
    {
        $formRepository = FormRepository::getInstance();
        $mode = $this->config ? 'config' : 'render';
        $formRepository->setMode($mode);
        $formRepository->setScenario($scenario);
        $formRepository->setPanel($panel);
        $formRepository->getFormQueryByParentQuery($baseQuery);
    }
}
