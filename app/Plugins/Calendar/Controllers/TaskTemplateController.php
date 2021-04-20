<?php

namespace App\Plugins\Calendar\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\Calendar\Handler\TaskWorkFlowHandler;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\TaskTemplate;
use App\Plugins\Calendar\Model\TemplateTask;
use App\Plugins\Calendar\Requests\TaskTemplateRequest;
use App\Plugins\Calendar\Requests\TemplateApplyRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = TaskTemplate::with(['templateTasks:template_id,id,name','category:id,name']);

        $query->when((bool)($request->searchTerm), function ($q) use ($request) {
            return $q->where('name', 'LIKE', "%$request->searchTerm%");
        });

        $taskTemplates =  $query
            ->select('id', 'name', 'category_id', 'description')
            ->orderBy((($request->sortField) ? : 'updated_at'), (($request->sortOrder) ? : 'asc'))
            ->paginate((($request->limit) ? : '10'))
            ->toArray();

        return successResponse('', $taskTemplates);
    }

    /**
     * Show the form for creating a new task template resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('Calendar::task-template-create-edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TaskTemplateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TaskTemplateRequest $request)
    {
        $incomingData = $request->only(['name','description','category_id']);

        $templateCreated = TaskTemplate::create($incomingData);

        return ($templateCreated)
            ? $this->linkTasks($templateCreated->id, $request->task_templates, false, $templateCreated->category_id)
            : errorResponse(trans('Calendar::lang.task-plugin-template-create-error'));
    }

    /*
     * @return \Illuminate\Http\JsonResponse
     */
    private function linkTasks($taskTemplateId, $taskTemplateFromRequest, $updating = false, $category = null)
    {
        if ($updating) {
            TaskTemplate::find($taskTemplateId)->templateTasks()->delete();
        }

        $dataForBatchInsert = [];

        foreach ($taskTemplateFromRequest as $template) {
            $dataForBatchInsert[] = [
                'name' => $template['taskName'],
                'template_id' => $taskTemplateId,
                'order' => $template['order'],
                'end' => $template['taskEnd'],
                'end_unit' => $template['taskEndUnit'],
                'assignees' => implode(',', $template['assignees']),
                'assign_task_to_ticket_agent' => $template['assignTaskToTicketAssignee'],
                'category' => $category
            ];
        }

        try {
            \DB::transaction(
                function () use ($dataForBatchInsert) {
                    TemplateTask::insert($dataForBatchInsert);
                }
            );
        } catch (\Throwable $e) {
            return errorResponse(trans('Calendar::lang.task-plugin-template-create-error'));
        }

        return successResponse(trans('Calendar::lang.task-plugin-template-created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $template = TaskTemplate::where('id', $id)->with(
            ['templateTasks:id,name,template_id,end,end_unit,assignees,order,assign_task_to_ticket_agent','category:id,name']
        )->firstOrFail();

        return view('Calendar::task-template-create-edit', ['template' => $template->toArray()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaskTemplateRequest $request, $id)
    {
        $template = TaskTemplate::find($id);

        if (!$template) {
            return errorResponse(trans('Calendar::lang.task-plugin-updating-template-which-does-not-exist'));
        }

        $updated = $template->update($request->only(['name','description','category_id']));

        return ($updated)
            ? $this->linkTasks($id, $request->task_templates, true, $request->category_id)
            : errorResponse(trans('Calendar::lang.task-plugin-template-create-error'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $template = TaskTemplate::find($id);

        if (!$template) {
            return errorResponse(trans('Calendar::lang.task-plugin-deleting-template-which-does-not-exist'));
        }

        $template->templateTasks()->delete();

        return ($template->delete())
            ? successResponse(trans('Calendar::lang.task-plugin-template-deleted'))
            : errorResponse(trans('Calendar::lang.task-plugin-template-delete-error'));
    }

    /**
     * Return settings form for task templates
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settings()
    {
        return view('Calendar::task-template-settings');
    }

    public function dropDownList(Request $request)
    {
        $limit = $request->limit ?: 10;
        $query = TaskTemplate::query();
        $searchTerm = request('search-query');
        $query->when((bool) $searchTerm, function ($q) use ($searchTerm) {
            return $q->where('name', 'LIKE', "%$searchTerm%");
        });
        return successResponse('', $query->simplePaginate($limit));
    }

    /**
     * @param TemplateApplyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyTemplate(TemplateApplyRequest $request)
    {
        if ($this->isTemplateAlreadyApplied($request->template, $request->ticketId)) {
            return errorResponse(trans('Calendar::lang.task-plugin-template-already-applied-to-ticket'));
        }

        $templateTasks = TemplateTask::where('template_id', $request->template)->orderBy('order')->get();

        if ($templateTasks->isEmpty()) {
            return errorResponse(trans('Calendar::lang.task-plugin-error-applying-tasks-from-template'));
        }

        (new TaskWorkFlowHandler())->handle($request->template, ['ticket_id' => $request->ticketId], $templateTasks);

        return successResponse(trans('Calendar::lang.task-plugin-applying-tasks-from-template-success'));
    }

    /**
     * Determines whether the selected template has already been applied to certain ticket
     * @param $templateId
     * @param $ticketId
     * @return bool
     */
    private function isTemplateAlreadyApplied($templateId, $ticketId)
    {
        return (bool) Task::where(['task_template_id' => $templateId,'ticket_id' => $ticketId])->count();
    }
}
