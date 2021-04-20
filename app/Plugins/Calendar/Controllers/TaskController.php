<?php

namespace App\Plugins\Calendar\Controllers;

use App\Http\Controllers\Common\PhpMailController;
use App\Model\MailJob\Condition;
use App\Plugins\Calendar\Handler\TaskNotificationHandler;
use App\Plugins\Calendar\Jobs\TaskNotificationJob;
use App\Plugins\Calendar\Jobs\TaskNotificationProcessorJob;
use App\Plugins\Calendar\Model\TaskAssignees;
use Auth;
use Lang;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\NavigationHelper;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Plugins\Calendar\Model\Task;
use Facades\App\Model\helpdesk\Settings\Alert;
use App\Model\helpdesk\Settings\System;
use App\Jobs\Notifications as NotifyQueue;
use App\Plugins\Calendar\Requests\TaskRequest;
use App\Http\Controllers\Common\Navigation\Navigation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskController extends Controller
{
    use NavigationHelper;
    
    /**
     * Return Calendar View.
     *
     * @return |\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $category = (request('category')) ?: 'all';
        return view('Calendar::taskViewAll', compact('category'));
    }

    /**
     * Gets task details by id
     * @param $id ID of the task
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskById($id)
    {
        $taskById = Task::with(['taskCategory','taskCategory.project','assignedTo','ticket:id,ticket_number'])->where('id', $id)->first();
        return ($taskById)
            ? successResponse('', $taskById->toArray())
            : errorResponse(trans('Calendar::lang.task_no_such_task'));
    }

    /**
     * Gets the task for Calendar View.
     * @return mixed
     */
    public function getTasks()
    {
        $query = (request('category') === 'assigned')
            ? $this->baseTaskQueryForAgents()
            : $this->baseTaskQuery();
        $tasks = $query->where('status', '!=', 'Closed');

        return $tasks->get(['id', 'task_name AS title','task_start_date','task_end_date','status'])
            ->unique('id');
    }

    /**
     * Gets simplified list of tasks for filter select field
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListOfTasks()
    {
        $query = (request('category') === 'assigned')
            ? $this->baseTaskQueryForAgents()
            : $this->baseTaskQuery();
        $searchTerm = request('search-query');
        $query->when((bool) $searchTerm, function ($q) use ($searchTerm) {
            return $q->where('task_name', 'LIKE', "%$searchTerm%");
        });
        return successResponse(
            '',
            $query->select('task_name AS name', 'id')->simplePaginate(request('limit') ?: 10)
        );
    }


    /**
     * This method filters the tasks based on the supplied paramaeters
     * @param \Illuminate\Http\Request $request
     * The following parameters should be encapsulated in \Illuminate\Http\Request object
     *  |-> string $is_private
     *  |-> string $is_completed
     *  |-> array  $ticket_ids
     *  |-> array  $projects
     *  |-> array  $task_lists
     *  |-> array $task_ids
     *  |-> array  $assigned_to
     *  |-> array  $createdByArray
     *  |-> string $sort_order
     *  |-> string $sort_field
     *  |-> string $search_term
     *  |-> string $limit
     *  |-> string $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function returnTasks(Request $request)
    {
        $query = ($request->has('category') && $request->category === 'assigned')
            ? $this->baseTaskQueryForAgents()
            : $this->baseTaskQuery();

        $this->applyTaskFilters($query, $request);

        $tasks =  $query
            ->select('id', 'task_name', 'created_by', 'task_category_id', 'task_description', 'created_at', 'ticket_id', 'status', 'task_start_date', 'task_end_date', 'task_template_id')
            ->orderBy((($request->sort_field) ? : 'updated_at'), (($request->sort_order) ? : 'asc'))
            ->paginate((($request->limit) ? : '10'))
            ->toArray();

        $tasks['tasks'] = $tasks['data'];
        unset($tasks['data']);

        return successResponse('', $tasks);
    }

    private function applyTaskFilters($query, $request)
    {
        if (!$request->status) {
            $query->where('status', '!=', 'Closed');
        } else {
            $query->whereIn('status', $request->status);
        }

        $query->when((bool)($request->isPrivate), function ($q) use ($request) {
            return $q->where('is_private', $request->isPrivate);
        });

        $query->when((bool)($request->created_by_array), function ($q) use ($request) {
            return $q->whereIn('created_by', $request->created_by_array);
        });

        $query->when((bool)($request->ticket_ids), function ($q) use ($request) {
            return $q->whereIn('ticket_id', $request->ticket_ids);
        });

        $query->when((bool)($request->task_ids), function ($q) use ($request) {
            return $q->whereIn('id', $request->task_ids);
        });

        $query->when((bool)($request->task_template), function ($q) use ($request) {
            return $q->whereIn('task_template_id', $request->task_template);
        });

        $query->when((bool)($request->projects), function ($q) use ($request) {
            return $q->whereHas('taskCategory.project', function ($qq) use ($request) {
                return $qq->whereIn('id', $request->projects);
            });
        });

        $query->when((bool)($request->task_categories), function ($q) use ($request) {
            return $q->whereHas('taskCategory', function ($qq) use ($request) {
                return $qq->whereIn('id', $request->task_categories);
            });
        });

        $query->when((bool)($request->assigned_to), function ($q) use ($request) {
            return $q->whereHas('assignedTo', function ($qq) use ($request) {
                return $qq->whereIn('user_id', $request->assigned_to);
            });
        });

        $query->when((bool)($request->search_term), function ($q) use ($request) {
            return $q->where('task_name', 'LIKE', "%$request->search_term%");
        });
    }

    /*
     * Formats the data obtained from Request to required format that can be persisted.
     * @param Request $request Request Object
     * @return array
     */
    private function formatRequestData(Request $request)
    {
        $data = $request->except(['_token', 'alert_repeat', 'assignee', 'task_alert','task_category_id']);
        $data['task_start_date'] = Carbon::parse($request->task_start_date, agentTimeZone(Auth::user()->id));
        $data['task_end_date'] = Carbon::parse($request->task_end_date, 'UTC');
        $data['task_category_id'] = $request->task_category_id ?: null;
        $data['is_private'] = $request->is_private;
        $data['status'] = ($request->status) ?: 'Open';

        if ($request->has('associated_ticket')) {
            $data['ticket_id'] = $request->associated_ticket;
        }

        return $data;
    }

    /**
     * Store a new task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TaskRequest $request)
    {
        try {
            $this->authorize('create', Task::class);
        } catch (\Exception $e) {
            return errorResponse(trans('Calendar::lang.change_status_auth_error'));
        }

        $data = $this->formatRequestData($request);

        $data['created_by'] = Auth::user()->id;

        $task = Task::create($data);

        if ($task) {
            $this->dispatcher($task, 'task-created');

            if ($request->assignee && count($request->assignee)) {
                $assigneesForInsert = [];

                foreach ($request->assignee as $assignee) {
                    $assigneesForInsert[] =  new TaskAssignees(["user_id" => $assignee]);
                }

                $task->assignedTo()->saveMany($assigneesForInsert);

                $this->dispatcher($task, 'task-assigned');
            }

            return successResponse(trans('Calendar::lang.task_created'));
        }
        return errorResponse("Calendar::lang.task_create_error");
    }

    public function dispatcher($task, $event, $agents = [])
    {
        (new PhpMailController())->setQueue();

        TaskNotificationJob::withChain([
           new TaskNotificationProcessorJob()
        ])->dispatch(new TaskNotificationHandler($task, $event, auth()->user(), $agents));
    }

    /**
     * Checks whether the logged in user has permission to modify the task
     * @param Task $task
     * @param $agents
     * @return bool
     */
    private function hasPermissionToModify(Task $task, $agents)
    {
        $hasPermission = true;
        $loggedInUser = Auth::user();
        if (($task->created_by->id != $loggedInUser->id)
            && !in_array($loggedInUser->id, $agents)
            && !$loggedInUser->role != 'admin'
        ) {
            $hasPermission = false;
        }
        return $hasPermission;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TaskRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaskRequest $request, Task $task)
    {
        try {
            $this->authorize('update', $task);
        } catch (\Exception $e) {
            return errorResponse(trans('Calendar::lang.change_status_auth_error'));
        }

        $newlyAssignedAgents = $existingAgents = [];
        $data = $this->formatRequestData($request);
        $agentsAssignedForTheTaskBeingUpdated =  $task->assignedTo->pluck('user_id')->toArray();

        $task->update($data);

        if ($task->is_private) {
            $this->dispatcher($task, 'task-update');

            $this->deleteAssignedAgents($task, $agentsAssignedForTheTaskBeingUpdated);

        } elseif ($request->has('assignee') && count($request->assignee)) {
            $newlyAssignedAgents = array_diff($request->assignee, $agentsAssignedForTheTaskBeingUpdated);

            $existingAgents = array_intersect($request->assignee, $agentsAssignedForTheTaskBeingUpdated);

            $agentsToBeDeleted = array_diff($agentsAssignedForTheTaskBeingUpdated, $request->assignee);

            $dataForBatchInserting = [];

            $this->deleteAssignedAgents($task, $agentsToBeDeleted);

            foreach ($newlyAssignedAgents as $newAgent) {
                $dataForBatchInserting[] = new TaskAssignees(['user_id' => $newAgent]);
            }

            $task->assignedTo()->saveMany($dataForBatchInserting);

            $this->dispatcher($task, 'task-update', $existingAgents);

            if ($newlyAssignedAgents) {
                $this->dispatcher($task, 'task-assigned', $newlyAssignedAgents);
            }
        }
        return successResponse(trans('Calendar::lang.task_updated'));
    }

    private function deleteAssignedAgents(Task $task, $agents)
    {
        /*
         * NOTE: Doing like below way instead of TaskAssignees::whereIn('user_id', $agents)->delete()
         * because the latter will delete rows using QueryBuilder but in order for Activity Log to work, Model
         * events are required so, deleting here using Model instance so that the `deleting` and `deleted`
         * events may fire.
         */
        TaskAssignees::whereIn('user_id', $agents)->where('task_id', $task->id)->get()->each(function ($assignee) {
            $assignee->delete();
        });
    }


    /**
     * Changes the task status
     * @param mixed $id
     * @param mixed $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus($id, $status)
    {
        $updated = 0;
        $task =  Task::find($id);

        if ($task) {
            $agentsAssignedForTheTaskBeingGettingStatusChanged =  $task->assignedTo->pluck('user_id')->toArray();

            if (!$this->hasPermissionToModify($task, $agentsAssignedForTheTaskBeingGettingStatusChanged)) {
                return errorResponse(trans('Calendar::lang.change_status_auth_error'));
            }

            if ($status == 'open') {
                if ($task->task_end_date <= Carbon::now()) {
                    return errorResponse(trans('Calendar::lang.change_status_reopen_error'));
                }
                $updated = $task->update(["status" => "Open"]);
            } elseif ($status == 'close') {
                $updated = $task->update(["status" => "Closed",'is_complete' => 1]);
            } elseif ($status == 'inprogress') {
                $updated = $task->update(["status" => "In-progress"]);
            }

            if ($updated) {
                $this->dispatcher($task, 'task-status');
                return successResponse(trans('Calendar::lang.status_changed'));
            }
            return errorResponse(trans('Calendar::lang.status_change_fail'));
        }
        return errorResponse(trans('Calendar::lang.task_no_such_task'));
    }

    /**
     * Injects navigation menu of task plugin
     * @param Collection $coreNavigationContainer
     */
    public function injectTaskAgentNavigation(Collection &$coreNavigationContainer)
    {
        $numberOfTasks = $this->baseTaskQuery()->where('status', '!=', 'Closed')->count();
        $numberOfAgentTasks = $this->baseTaskQueryForAgents()->where('status', '!=', 'Closed')->count();

        $assignedTaskNavigation = $this->createNavigation(
            'my_task',
            'glyphicon glyphicon-th-list',
            true,
            $numberOfAgentTasks,
            '/tasks/task?category=assigned',
            'task'
        );
        $allTaskNavigation = $this->createNavigation(
            'task-all-tasks',
            'fa fa-tasks',
            true,
            $numberOfTasks,
            '/tasks/task?category=all',
            'task'
        );

        $calendarNavigation = collect([$assignedTaskNavigation,$allTaskNavigation]);
        $coreNavigationContainer->push(
            $this->createNavigationCategory(Lang::get('Calendar::lang.tasks'), $calendarNavigation)
        );
    }

    /**
     * Creates the navigation for task plugin
     * @param $name
     * @param $iconClass
     * @param $hasCount
     * @param $count
     * @param $redirectUrl
     * @param $routeString
     * @return Navigation
     */
    private function createNavigation($name, $iconClass, $hasCount, $count, $redirectUrl, $routeString)
    {
        $navigation = new Navigation();
        $navigation->setName(Lang::get("Calendar::lang.$name"));
        $navigation->setIconClass($iconClass);
        $navigation->setHasCount((bool)$hasCount);
        $navigation->setCount((int)$count);
        $navigation->setRedirectUrl($redirectUrl);
        $navigation->setRouteString($routeString);
        return $navigation;
    }

    /**
     * return view for editing tasks
     * @param Task $task
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit(Task $task)
    {
        try {
            $this->authorize('update', $task);
        } catch (\Exception $e) {
            return redirect('dashboard');
        }
        return view('Calendar::taskEdit');
    }

    /** Returns base query for tasks
     * @return mixed
     */
    private function baseTaskQuery()
    {
        $authUserId = Auth::user()->id;
        return Task::with(['ticket:id,ticket_number','taskCategory:id,name','taskTemplate:name,id'])->where(
            function ($taskQuery) use ($authUserId) {
                $taskQuery->where('created_by', $authUserId);
                $taskQuery->orWhereHas(
                    'assignedTo',
                    function ($taskSubQuery) use ($authUserId) {
                        $taskSubQuery->where('user_id', $authUserId);
                    }
                );
            }
        );
    }

    /**
     * Gets all the tasks belongs to a particular ticket
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTicketTasks(Request $request)
    {
        $query = Task::where([['ticket_id','=', $request->ticket_id], ['status','!=','Closed']])->with('taskTemplate:name,id');

        $query->when((bool)($request->search_term), function ($q) use ($request) {
            return $q->where('task_name', 'LIKE', "%$request->search_term%");
        });

        $tasks =  $query
            ->select('id', 'task_name', 'task_description', 'status', 'task_start_date', 'task_end_date', 'task_template_id')
            ->orderBy((($request->sort_field) ? : 'updated_at'), (($request->sort_order) ? : 'asc'))
            ->paginate((($request->limit) ? : '10'))
            ->toArray();

        $tasks['tasks'] = $tasks['data'];
        unset($tasks['data']);

        return successResponse('', $tasks);
    }

    /**
     * Returns base task query for assigned agents
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function baseTaskQueryForAgents()
    {
        return Task::with(['ticket:id,ticket_number','taskCategory:id,name','taskTemplate:name,id'])
            ->whereHas('assignedTo', function ($q) {
                return $q->where('user_id', Auth::user()->id);
            });
    }

    /**
     * returns the view which shows list of tasks.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function returnTaskViewPage()
    {
        $category = (request('category'));
        return view('Calendar::taskViewAll', compact('category'));
    }

    /**
     * returns view for task settings
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewWithTaskSettings()
    {
        return view('Calendar::taskSettings');
    }

    /**
     * returns task show page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Task $task)
    {
        try {
            $this->authorize('view', $task);
        } catch (\Exception $e) {
            return redirect('dashboard');
        }
        return view('Calendar::taskIndView');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        try {
            $this->authorize('create', Task::class);
        } catch (\Exception $e) {
            return redirect('dashboard');
        }
        return view('Calendar::taskCreate');
    }

    public function getCalendarTaskPage()
    {
        $category = (request('category')) ?: 'all';
        return view("Calendar::tasks.task-list", compact('category'));
    }

    public function destroy(Task $task)
    {
        try {
            $this->authorize('delete', $task);
        } catch (\Exception $e) {
            return errorResponse(trans('Calendar::lang.change_status_auth_error'));
        }

        $task->assignedTo()->delete();

        return ($task->delete())
            ? successResponse(trans('Calendar::lang.task_deleted'))
            : errorResponse(trans('Calendar::lang.task_not_deleted'));
    }

    public function sendReminders()
    {
        $tasks = Task::where('status', '!=', 'Closed')
            ->get(['id','task_name','task_end_date','created_by']);
        foreach ($tasks as $task) {
            if (Carbon::parse($task->task_end_date) < Carbon::now('UTC')) {
                continue;
            }

            $this->dispatcher($task, 'task-reminder', []);
        }
    }
}
