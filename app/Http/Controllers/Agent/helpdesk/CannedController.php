<?php

namespace App\Http\Controllers\Agent\helpdesk;

// controllers
use App\Facades\Attach;
use App\Http\Controllers\Controller;
use App\FaveoStorage\Controllers\AttachmentStoreController;
// requests
use App\Http\Requests\helpdesk\CannedRequest;
use Illuminate\Http\Request;
use App\Http\Requests\helpdesk\CannedUpdateRequest;
// model
use App\Model\helpdesk\Agent_panel\Canned;
use App\Model\helpdesk\Agent_panel\DepartmentCannedResponse as DeptCann;
use App\Model\helpdesk\Agent\DepartmentAssignAgents;
use App\Model\helpdesk\Agent\Department;
use App\User;
// classes
use Exception;
use Lang;
use Auth;

/**
 * CannedController.
 *
 * This controller is for all the functionalities of Canned response for Agents in the Agent Panel
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class CannedController extends Controller
{
    /**
     * Create a new controller instance.
     * constructor to check
     * 1. authentication
     * 2. user roles
     * 3. roles must be agent.
     *
     * @return void
     */
    public function __construct()
    {
        // checking authentication
        $this->middleware('auth');
        // checking if role is agent
        $this->middleware('role.agent');
    }

    /**
     * Display a listing of the Canned Responses.
     *
     * @return type View
     */
    public function index()
    {
        try {
            $Canneds = $this->getCannedBuilder(\Auth::user()->id);
            return view('themes.default1.agent.helpdesk.canned.index', compact('Canneds'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    
    /**
     * Get all the canned responses into the datatable
     *
     * @author Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
     *
     * @date   2019-05-28T18:34:02+0530
     *
     * @param  Request $request
     *
     * @return json
     */
    public function getIndex(Request $request)
    {
        try {
            $pagination = ($request->input('limit')) ? $request->input('limit') : 10;
            $sortBy = ($request->input('sort-field')) ? $request->input('sort-field') : 'id';
            $search = $request->input('search-query');
            $orderBy = ($request->input('sort-order')) ? $request->input('sort-order') : 'id';
            $canned = $this->getCannedBuilder(Auth::user()->id)->orderBy($sortBy, $orderBy);
            $searchQuery = $canned->where('title', 'LIKE', '%' . $search . '%')->paginate($pagination);
            return successResponse('', $searchQuery);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new Canned Response.
     *
     * @return type View
     */
    public function create()
    {
        try {
            return view('themes.default1.agent.helpdesk.canned.create');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }


    /**
     *
     * @return type
     */
    public function edit()
    {
        return view('themes.default1.agent.helpdesk.canned.edit');
    }

    /**
     * Show the form for editing the Canned Response.
     *
     * @param type        $id
     * @param type Canned $canned
     *
     * @return type View
     */
    public function editApi($id, Canned $canned)
    {
        try {
            $found = 0;
            $Canneds = $this->getCannedBuilder(Auth::user()->id)->get();
            foreach ($Canneds->toArray() as $value) {
                if (array_search($id, $value)) {
                    $found = 1;
                    break;
                }
            }
            if ($found == 1) {
                $canned = $canned->where('id', $id)->first();

                $attachments = $canned->linkedAttachments()->get()->toJson();
                // fetching requested canned response
                $cannDeptLists = $canned->departments()->select('name', 'department.id')->get()->toArray();
                $canned['share'] = (count($cannDeptLists) > 0) ? true : false;
                $canned['departments'] = $cannDeptLists;
                
                return successResponse('', $canned);
            } else {
                return errorResponse(Lang::get('lang.not-autherised'));
            }
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Create and Update the canned Response
     *
     * @date   2019-06-13T10:48:26+0530
     *
     * @param  CannedRequest $request
     * @param  Canned $canned
     *
     * @return json
     */
    public function createUpdateCannedResponse(CannedRequest $request, Canned $canned)
    {
        try {
            /* select the field where id = $id(request Id) */
            // fetching all the requested inputs
            $canned->user_id = \Auth::user()->id;
            $canned->title = $request->input('title');
            $canned->message = $request->input('message');
            // saving inputs
            $canned = $canned->updateOrCreate(['id' => $request->canned_id], $canned->toArray());

            if ($request->input('share') == true) {
                $dept_ids = Department::whereIn('id', $request->get('d_id[]'))->pluck('id');
                DeptCann::select('id')->whereNotIn('dept_id', $dept_ids)->where('canned_id', $canned->id)->delete();
                foreach ($dept_ids as $dept_id) {
                    DeptCann::updateOrCreate([
                        'dept_id' => $dept_id,
                        'canned_id' => $canned->id,
                    ]);
                }
            } else {
                DeptCann::where('canned_id', '=', $canned->id)->delete();
            }
            $this->storeCannedAttachments($canned, $request->get('inline'),$request->get('attachment'));
            $response = $request->canned_id ? Lang::get('lang.canned_response_updated_successfully') : Lang::get('lang.canned_response_saved_successfully');
            return successResponse($response);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Delete the Canned Response from storage.
     *
     * @param type        $id
     * @param type Canned $canned
     *
     * @return type Redirect
     */
    public function destroy($id, Canned $canned, DeptCann $dept_cann)
    {
        try {
            $dept_cann = $dept_cann->where('canned_id', '=', $id)->delete();
            /* delete the selected field */
            if($canned->whereId($id)->delete()) {
                /* redirect to Index page with Success Message */
                return successResponse(Lang::get('lang.canned_deleted_successfully'));   
            }

            return errorResponse(trans('lang.not_found'), FAVEO_NOT_FOUND_CODE);
        } catch (Exception $e) {
            /* redirect to Index page with Fails Message */
            return errorResponse($e->getMessage());
        }
    }

    /**
     * NOTE : it has to be depreciated after new-inbox is finalized
     * Fetch Canned Response in the ticket detail page.
     *
     * @param type $id
     *
     * @return type json
     */
    public function getCanned($id)
    {
        $responseValues = [];
        $cannedResponses = $this->getCannedBuilder($id)->get();
        if ($cannedResponses) {
            foreach ($cannedResponses as $cannedResponse) {
                array_push($responseValues, [$cannedResponse->id, $cannedResponse->title]);
            }
        }
        if (sizeof($responseValues) != 0) {
            $response = $responseValues;
        } else {
            $response = [['zzz', 'select canned_response']];
        }
        return json_encode($response);
    }

    /**
     * NOTE : it has to be depreciated after new-inbox is finalized
     * @category function to get querybuilder collection of canned responses
     * @param type int $id //id of an agent
     * @var $dept_id, $dept_cann, $user_departments, $user_department, $user_dept_canns, $user_dept_cann, $canned_responses
     * @return builder
     */
    public function getCannedBuilder($id)
    {
        $dept_id = [];
        $dept_cann = [];
        $user_departments = DepartmentAssignAgents::select('department_id')->where('agent_id', '=', $id)->get();
        if ($user_departments) {
            foreach ($user_departments as $user_department) {
                array_push($dept_id, $user_department->department_id);
            }
        }
        $user_dept_canns = DeptCann::select('canned_id')->whereIn('dept_id', $dept_id)->groupBy('canned_id')->orderBy('canned_id')->get();
        if ($user_dept_canns) {
            foreach ($user_dept_canns as $user_dept_cann) {
                array_push($dept_cann, $user_dept_cann->canned_id);
            }
        }
        $canned_responses = Canned::select('id', 'title', 'created_at', 'updated_at')->where(function ($q) use ($id, $dept_cann) {
            $q->where('user_id', $id)->orWhereIn('id', $dept_cann)->groupBy('id')->orderBy('id');
        });
        return $canned_responses;
    }

    /**
     * @category function to send response to ajax call with depratment names and id to which the calling
     *canned response is shared with
     * @param int $id
     * @var $dept, $dept_id, $departments, $department_json
     * @return type json object $departments_json
     */
    public function getCannedDepartments($id)
    {
        $dept = '';
        $dept_id = [];
        $departments = DeptCann::select('dept_id')->where('canned_id', '=', $id)->get();
        if ($departments) {
            foreach ($departments as $department) {
                array_push($dept_id, $department->dept_id);
            }
        }
        if (!empty($dept_id)) {
            $dept = Department::select('name as name_id', 'name as text')
                ->whereIn('id', $dept_id)->get();
        }
        $dropdown_controller = new \App\Http\Controllers\Agent\helpdesk\DropdownController();
        $departments_json = $dropdown_controller->formatToJson($dept, 'department-list');
        return $departments_json;
    }

    /**
     * @category function to send canned response message body
     * @param int $id
     * @
     * @return string HTML code
     */
    public function getCannedMessage($cannedId)
    {
        $canned = Canned::where('id', '=', $cannedId)->first();
        if(!$canned) return errorResponse(trans('lang.not_found'), FAVEO_NOT_FOUND_CODE);
        $linkedAttachments = $canned->attachments()->whereNotIn('disposition', ['inline', 'INLINE'])->get(['name', 'path','driver']);
        $linkedInlineAttachments = $canned->attachments()->whereNotIn('disposition', ['attachment', 'ATTACHMENT'])->get(['name', 'path'])->toArray();
        $attachments = $this->formatAttachments($linkedAttachments);
        $inline = $this->formatAttachments($linkedInlineAttachments);
        
        return successResponse('', ['title' => $canned->title,'message' => $canned->message, 'attachments' => $attachments, 'inline' => $inline]);
    }

    /**
     * Function to return attachments in formatted array
     * @param   $linkedAttachments  iterable containing attachment details
     * @return  array formatted array of attachments
     */
    public function formatAttachments($linkedAttachments = [])
    {
        $attachments = [];
        foreach ($linkedAttachments as $key => $file) {
            $fullPath = Attach::getFullPath($file->getOriginal('name'), $file->driver);
            $attachments[$key]['name']      = basename($fullPath);
            $attachments[$key]['filename']  = $file->getOriginal('name');
            $attachments[$key]['disk']      = $file->driver;
            $attachments[$key]['size']      = Attach::getSizeOfPath($file->getOriginal('name'), $file->driver);
            $attachments[$key]['type']      = pathinfo($file->getOriginal('name'), PATHINFO_EXTENSION);
            $attachments[$key]['path']      = strstr($fullPath, $file->getOriginal('name'), true) ?: $fullPath;
        }

        return $attachments;
    }

    /**
     * Function to store inline and attachment files infromation in database
     * @param  Canned   $canned
     * @param  Array    $inline       canned inline attachments submitted by users
     * @param  Array    $attachments  canned attachments submitted by users
     */
    public function storeCannedAttachments(Canned $canned, array $inline = [], array $attachments = [])
    {
        try {
            $attachedId = [];
            foreach ($inline as $key => $value) {
            $attachedId[] = ['attachment_id' => (new AttachmentStoreController)->storeAttachments($value, 'inline')];
        }

            foreach ($attachments as $key => $value) {
                $attachedId[] = ['attachment_id' => (new AttachmentStoreController)->storeAttachments($value, 'attachment')];
            }
            $toAdd = [];
            foreach ($attachedId as $key => $value) {
                array_push($toAdd, $value['attachment_id']);
            }
            $canned->linkedAttachments()->whereNotIn('attachment_id', $toAdd)->delete();
            foreach ($attachedId as $value) {
                $canned->linkedAttachments()->updateOrCreate($value, $value);
            }
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
}
