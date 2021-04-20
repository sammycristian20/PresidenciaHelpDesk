<?php

namespace App\Plugins\Calendar\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plugins\Calendar\Model\Project;
use App\Plugins\Calendar\Requests\ProjectRequest;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('role.admin')->except(['index']);
    }

    public function index(Request $request)
    {
        $limit = $request->limit ?: 10;
        $query = Project::query();
        $searchTerm = request('search-query');
        $query->when((bool) $searchTerm, function ($q) use ($searchTerm) {
            return $q->where('name', 'LIKE', "%$searchTerm%");
        });
        return successResponse('', $query->simplePaginate($limit));
    }

    /**
     * Stores the Project Resource
     * @param ProjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProjectRequest $request)
    {
        Project::create($request->all());
        return successResponse(trans('Calendar::lang.project_created'));
    }

    /**
     * Destroys the project Resource
     * @param $projectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($projectId)
    {
        $project = Project::where('id', $projectId)->first();

        if (!$project) {
            return errorResponse(trans('Calendar::lang.task-plugin-modifying-non-existent-project'));
        }

        $project->categories()->delete();

        return ($project->delete())
            ? successResponse(trans('Calendar::lang.project_deleted'))
            : errorResponse(trans('Calendar::lang.project_not_deleted'));
    }

    public function edit($projectId, ProjectRequest $request)
    {
        $project = Project::where('id', $projectId);

        $updatedRow = $project->update($request->all());

        return ($updatedRow)
            ? successResponse(trans('Calendar::lang.project_updated'))
            : errorResponse(trans('Calendar::lang.project_not_updated'));
    }

    public function returnProjects(Request $request)
    {
        $query = Project::query();

        $query->when((bool)($request->searchTerm), function ($q) use ($request) {
            return $q->where('name', 'LIKE', "%$request->searchTerm%");
        });

        $query->when((bool)($request->projectIds), function ($q) use ($request) {
            return $q->whereIn('id', $request->projectIds);
        });

        $projects =  $query
                ->select('id', 'name', 'created_at')
                ->orderBy((($request->sortField) ? : 'created_at'), (($request->sortOrder) ? : 'asc'))
                ->paginate((($request->limit) ? : '10'))
                ->toArray();

        $projects['projects'] = $projects['data'];
        unset($projects['data']);

        return successResponse('', $projects);
    }

    /**
     * returns view for project editing
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editForm()
    {
        return view('Calendar::projectEdit');
    }
}
