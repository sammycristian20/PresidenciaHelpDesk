<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Common\Dependency\DependencyDetails;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use App\Model\helpdesk\TicketRecur\Recur;
use App\Model\helpdesk\TicketRecur\RecureContent;
use App\Http\Requests\helpdesk\TicketRecurRequest;
use \Carbon\Carbon;
use App\Traits\EnhancedDependency;
use Lang;
use Auth;
use URL;

class TicketRecurController extends Controller
{
    /**
     * Recure model id
     * @var int
     */
    public $recure_id;

    /**
     * Requester of recuring
     * @var array
     */
    public $requester;

    /**
     * Subject of the ticket
     * @var string
     */
    public $subject;

    /**
     * Body of the ticket
     * @var string
     */
    public $body;

    /**
     * Help tipic id of the ticket
     * @var int
     */
    public $help_topic;

    /**
     * Priority id of the ticket
     * @var int
     */
    public $priority;

    /**
     * Type of the ticket
     * @var int
     */
    public $type;

    /**
     * Ticket assigned agent
     * @var int
     */
    public $assigned;

    /**
     * Status id of the ticket
     * @var int
     */
    public $status;

    /**
     * Collaborators of the ticket
     * @var array
     */
    public $cc;

    /**
     * Company id associated with requester
     * @var id
     */
    public $company;

    /**
     * Attachment of the ticket
     * @var array
     */
    public $attachments;

    /**
     * Custom field of the ticket
     * @var array
     */
    public $extra_form;

    /**
     * Department of the ticket
     * @var int
     */
    public $department;

    /**
     * Source id of the ticket
     * @var int
     */
    public $source;

    /**
     * Inline images of the ticket
     * @var array
     */
    public $inline;

    /**
     * Extra attachment to the ticket (Custom attachment field)
     * @var array
     */
    public $files;

    public $recurPermission;

    public $panel;

    public function __construct()
    {
        $this->mailController()->setQueue();
    }

    /**
     * Get the value from the RecureContent model
     * @param string $option
     * @return RecureContent
     */
    public function getOptions($option)
    {
        return RecureContent::where('recur_id', $this->recure_id)->where('option', $option)->value('value');
    }
    /**
     * Set the requester parameter
     * @return void
     */
    public function setRequester()
    {
        $this->requester = $this->getOptions('requester');
    }
    /**
     * Set the subject parameter
     * @return void
     */
    public function setSubject()
    {
        $this->subject = $this->getOptions('subject');
    }
    /**
     * Set the body parameter
     * @return void
     */
    public function setBody()
    {
        $this->body = $this->getOptions('description');
    }
    /**
     * Set the helptopic of the ticket
     * @return void
     */
    public function setHelptopic()
    {
        $this->help_topic = $this->getOptions('help_topic_id');
        if (!$this->help_topic) {
            $ticket_controller = new \App\Http\Controllers\Agent\helpdesk\TicketController();
            $this->help_topic =  $ticket_controller->getSystemDefaultHelpTopic();
        }
    }
    /**
     * Set the department of the ticket
     * @return void
     */
    public function setDepartment()
    {
        $department = $this->getOptions('department_id');
        if (!$department && $this->help_topic) {
            $department = departmentByHelptopic($this->help_topic);
        }
        $this->department = $department;
    }
    /**
     * Set the source of the ticket
     * @return void
     */
    public function setSource()
    {
        $source = $this->getOptions('source_id');
        if (!$source) {
            $source = \App\Model\helpdesk\Ticket\Ticket_source::select('id')->value('id');
        }
        $this->source = $source;
    }
    /**
     * Set the priority of the ticket
     * @return void
     */
    public function setPriority()
    {
        $this->priority = $this->getOptions('priority_id');
    }
    /**
     * Set the type of the ticket
     * @return void
     */
    public function setType()
    {
        $this->type = $this->getOptions('type_id');
    }
    /**
     * Set the assigned agent of the ticket
     * @return void
     */
    public function setAssigned()
    {
        $this->assigned = $this->getOptions('assigned_id');
    }
    /**
     * Set the status of the ticket
     * @return void
     */
    public function setStatus()
    {
        $this->status = $this->getOptions('status_id');
    }
    /**
     * Set the collaborator of the ticket
     * @return void
     */
    public function setCc()
    {
        $this->cc = [];
        $ccIDs = $this->getOptions('cc');
        if($ccIDs && is_array($ccIDs)){
            $this->cc =  User::whereIn('id',$ccIDs)->pluck('email')->toArray();
        }
    }
    /**
     * Set the company of the ticket
     * @return void
     */
    public function setCompany()
    {
        $this->company = $this->getOptions('company');
    }
    /**
     * Set the attachment of the ticket
     * @return void
     */
    public function setAttachments()
    {
        $this->attachments = [];
        $json              = $this->getOptions('media_attachment');
        if ($json) {
            $this->attachments = json_decode($json, true);
        }
    }
    /**
     * Set the online of the ticket
     * @return void
     */
    public function setInline()
    {
        $this->inline = [];
        $json         = $this->getOptions('inline');
        if ($json) {
            $this->inline = json_decode($json, true);
        }
    }
    /**
     * Set the extra files of the ticket
     * @return void
     */
    public function setFiles()
    {
        $this->files = [];
        $json        = $this->getOptions('files');
        if ($json) {
            $this->files = json_decode($json, true);
        }
    }


    /**
    * Set the type of the ticket
    * @return void
    */
    public function setLocation()
    {
        $this->location = $this->getOptions('location_id');
    }
    /**
     * Set the custom field values of the ticket
     * @return void
     */
    public function setExtraForm()
    {
        $this->extra_form = RecureContent::where('recur_id', $this->recure_id)
              ->whereNotIn('option', [
                  'requester', 'subject', 'body', 'help_topic_id', 'priority_id', 'type_id', 'location_id',
                  'assigned_id', 'status_id', 'cc', 'company', 'attachments', 'description', 'department_id',
              ])
              ->pluck('value', 'option')
              ->toArray();
        // loop over extra_content and pull all files and append that in attachment
        foreach ($this->extra_form as $field => &$element) {
            //if it has storage path as substring, then only it will consider that as attachment
            if (is_array($element)) {
                foreach ($element as &$value) {
                    if (is_string($value) && file_exists($value) && strpos($value, storage_path()) !== false) {
                        $file = (object)[];
                        $file->filePath = $value;
                        $this->attachments[] = $file;
                        unset($this->extra_form[$field]);
                    }
                }
            }
        }
    }

    /**
     * Create ticket via Job
     * @param int $recur_id
     * @return void
     */
    public function createTicket($recur_id, $allowDelay=true, $parentTicket = null)
    {
        $this->recure_id = $recur_id;
        $this->setAssigned();
        $this->setAttachments();
        $this->setInline();
        $this->setBody();
        $this->setCompany();
        $this->setExtraForm();
        $this->setHelptopic();
        $this->setDepartment();
        $this->setPriority();
        $this->setStatus();
        $this->setSubject();
        $this->setType();
        $this->setSource();
        $this->setFiles();
        $this->setLocation();
        $delayByMinutes = 0;
        if($allowDelay) {
            $delayByMinutes = $this->getMinutesToDelayRecurExecution();
            $this->setCc();
            $this->setRequester();
        }
        $tickets = $this->ticketValues($this->requester);

        ($parentTicket) ? $tickets['parent_ticket_id'] = $parentTicket : null;

        dispatch(new \App\Jobs\RecurTicket($tickets))->delay(now()->addMinutes($delayByMinutes))->onQueue('recurring');
    }
    /**
     * Arrange the ticket parameters
     * @param int $requester
     * @return array
     */
    public function ticketValues($requester)
    {

        // dd($this->extra_form);
        $tickets = [
            'requester'     => $requester,
            'subject'       => $this->subject,
            'help_topic'    => $this->help_topic,
            'body'          => $this->body,
            'sla'           => '',
            'priority'      => $this->priority,
            'source'        => $this->source,
            'cc'            => $this->cc,
            'dept'          => $this->department,
            'assigned'      => $this->assigned,
            'status'        => $this->status,
            'extra_form'    => $this->extra_form,
            'type'          => $this->type,
            'attachments'   => array_merge($this->attachments, $this->files),
            'inline'        => $this->inline,
            'email_content' => "",
            'company'       => $this->company,
        ];

        //added to fulfill ACT Requirement
        $userLocation = User::where('id', $requester)->value('location');
        $tickets['location'] = ($this->location) ?: $userLocation;

        return $tickets;
    }

    /**
     * Get the index page of the Recure
     * @return \Response
     */
    public function index()
    {
        try {
            $this->permissionAndPanelCheck();
            if (!$this->recurPermission) {
                return redirect('dashboard')->with('fails', Lang::get('lang.permission-denied-recur'));
            }

            $items = $this->getRecurs();
          
            return view('themes.default1.admin.helpdesk.recur.index', compact('items'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function getIndex(Request $request)
    {
        $this->permissionAndPanelCheck();
        if (!$this->recurPermission) {
            return errorResponse(Lang::get('lang.permission-denied-recur'));
        }
        $pagination = ($request->input('limit')) ? $request->input('limit') : 10;
        $sortBy = ($request->input('sort-field')) ? $request->input('sort-field') : 'id';
        $search = $request->input('search-query');
        $orderBy = ($request->input('sort-order')) ? $request->input('sort-order') : 'desc';
        $recur = $this->getRecurs()->orderBy($sortBy, $orderBy);
        $searchQuery = $recur->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('interval', 'LIKE', '%' . $search . '%');
        })
        ->paginate($pagination);
        return successResponse('', $searchQuery);
    }

    /**
     * Get the recurs based on user role and panel
     * @return $items
     */
    private function getRecurs()
    {
        $auth = Auth::user();
        $recur = new Recur();
        if ($auth->where([['id', $auth->id], ['role', 'agent']])->count()) {
            $items = $recur->where([
                ['user_id', $auth->id],
                ['type', 'agent_panel']
            ])->select('interval', 'start_date', 'end_date', 'name', "id");
        } else if ($auth->where([['id', $auth->id], ['role', 'admin']])->count() && $this->panel == 'agent_panel') {
            $items = $recur->with('content')->where('type', 'agent_panel')->select('id', 'interval', 'start_date', 'end_date', 'name');
        } else if ($auth->where([['id', $auth->id], ['role', 'admin']])->count() && $this->panel == 'admin_panel') {
            $items = $recur->with('content')->where('type', 'admin_panel')->select('id', 'interval', 'start_date', 'end_date', 'name');
        }

        return $items;
    }
    /**
     * Get the create page of the Recure
     * @return \Response
     */
    public function create()
    {
        try {
                $this->permissionAndPanelCheck();
                if (!$this->recurPermission) {
                    return redirect('dashboard')->with('fails', Lang::get('lang.permission-denied-recur'));
                }
                return view('themes.default1.admin.helpdesk.recur.create');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    /**
     * Get the edit page of the Recure
     * @return \Response
     */
    public function edit($recurId)
    {
        try {
                return view('themes.default1.admin.helpdesk.recur.edit', compact('recurId'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Modifies ticket object, if file is found, it uploads that file, write its path to the variable value
     * @param  array &$ticketFields  associative array of fields with values
     * @return null
     */
    private function handleAttachments(array &$ticketFields)
    {
        foreach ($ticketFields as $field => &$values) {
            //if value is an array, check for files
            if (is_array($values)) {
                //if value is a file
                foreach ($values as &$value) {
                    // if it is a file
                    if (is_file($value)) {
                        // $value = (object)$value;
                        //move the file to storage folder
                        $baseStoragePath = storage_path().DIRECTORY_SEPARATOR.'app';

                        if (!file_exists($baseStoragePath)) {
                            mkdir($baseStoragePath);
                        }

                        $basePath = $baseStoragePath.DIRECTORY_SEPARATOR.'recur';
                        if (!file_exists($basePath)) {
                            mkdir($basePath);
                        }

                        $fileName = str_random(5).'_'.$value->getClientOriginalName();
                        uploadInLocal($value, $basePath, $fileName);
                        $value = $basePath. DIRECTORY_SEPARATOR. $fileName;
                    }
                }
            }
        }
    }

    /**
     * Appends attachment in passed array if ticketFields
     * @param  array  &$ticketFields  associative array of ticket fields
     * @return null
     */
    private function appendRecurAttachments(array &$ticketFields)
    {
        foreach ($ticketFields as $field => &$element) {
            //if it has storage path as substring, then only it will consider that as attachment
            if (is_array($element)) {
                foreach ($element as &$value) {
                    if (is_string($value) && file_exists($value) && strpos($value, storage_path()) !== false) {
                        $fileObject = (object)[];
                        $fileObject->file = base64_encode(file_get_contents($value));
                        $fileObject->filename = basename($value);
                        // $fileObject->size = getSize(filesize($value));
                        // $fileObject->type = mime_content_type($value);
                        $value = $fileObject;
                    }
                }
            }
        }
    }

    /**
     * Save Recure Model
     * @return \Response
     */
    public function createOrUpdateRecur(TicketRecurRequest $request)
    {
        try {
            $recur       = $request->input('recur');
            $ticket      = $request->except(['recur']);

            $this->handleAttachments($ticket);
            $recurId = $request->input('recur.id');
            $recurExistingEntry = Recur::find($recurId);
            $recur_model = $recurExistingEntry ? $recurExistingEntry : new Recur;
            $this->recurSave($recur, $ticket, $recur_model, $request);
            $message     = trans('lang.saved_successfully');
            
            return successResponse($message);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Gets recur ticket data
     * @param  Request $request
     * @return Response
     */
    public function getRecurTicket($recurId)
    {
        $recur = Recur::select('id', 'interval', 'delivery_on', 'start_date', 'end_date', 'name', 'execution_time', 'type')
                ->where('id', $recurId)->first();

//        $recur->interval = ['id'=> $recur->interval, 'name'=>Lang::get("lang.$recur->interval")];
//        $recur->interval = ['id'=> $recur->interval, 'name'=>Lang::get("lang.$recur->interval")];

        $this->permissionAndPanelCheck(true);
        if (!$recur) {
            return errorResponse(trans('lang.not_found'));
        }
        else if ($this->recurPermission && $this->panel == 'agent_panel' && $recur->type == 'admin_panel') {
            return errorResponse(Lang::get('lang.cant_access_admin_panel_recur'),404);
        }
        else if ($this->recurPermission && $this->panel == 'admin_panel' && $recur->type == 'agent_panel') {
            return errorResponse(Lang::get('lang.cant_access_agent_panel_recur'),404);
        }

        $recurTicketContent = RecureContent::where('recur_id', $recurId)->get();
        $formattedRecurTicketContent = [];

        /*
         * To get the object value of the dependency_id, we are passing instance of DependencyDetails
         * for eg. if key is status_id and value is 1, we need to get {'status'=> {'id'=>1, 'name'=>'Open'}}
         */
        $dependencyDetailInstance = new DependencyDetails();

        foreach ($recurTicketContent as $element) {
            // if not coming as empty array, replace the current key else not
            $elementArray = $this->formatRecurTicket($element->option, $element->value, $dependencyDetailInstance);

            if (is_array($elementArray)) {
                $formattedRecurTicketContent = array_merge($elementArray, $formattedRecurTicketContent);
            } else {
                $formattedRecurTicketContent[$element->option] = $element->value;
            }
        }
        $this->appendRecurAttachments($formattedRecurTicketContent);

        $editForm = (new \App\Http\Controllers\Utility\FormController())
            ->getFormWithEditValues($formattedRecurTicketContent, 'ticket', 'recur', 'agent', $recurId);

        $editForm->recur = $recur;

        return successResponse('', $editForm);
    }

    /**
     * Formats ticket in required format
     * NOTE: this is a helper method for `getRecurTicket` and should not be used at any other place
     * @param string $key
     * @param string $value
     * @param DependencyDetails $dependencyDetails
     * @return array associative array of of field required for eg ['status'=> ['id'=>1, 'name'=>'Open']]
     */
    private function formatRecurTicket($key, $value, DependencyDetails $dependencyDetails)
    {
        /*
         * storing the mapping of id keys and edit keys.
         * for eg, when storing recur values, we store as status_id. But while editing frontend requires
         * status as key, with its value (['status'=> ['id'=>1, 'name'=>'Open']])
         */
        $mapping = ['status_id'=> 'status', 'department_id'=>'department', 'help_topic_id'=> 'help_topic',
            'priority_id'=>'priority', 'assigned_id'=>'assigned', 'location_id'=> 'location',
            'source_id'=> 'source', 'type_id'=> 'type', 'requester'=> 'requester', 'cc'=>'cc'];

        // field which requires meta to be true. Helptopic and departments has child fields, which can be obtained by passing meta as true
        $metaDependencies = ["help_topic_id", "department_id"];

        try {
            switch ($key) {
                case 'status_id':
                case 'department_id':
                case 'help_topic_id':
                case 'priority_id':
                case 'assigned_id':
                case 'location_id':
                case 'source_id':
                case 'type_id':
                case 'requester':
                    $dependencyDetails->setOutputAsObject(true);
                    return[$mapping[$key] => $dependencyDetails->getDependencyDetails($key, $value, in_array($key, $metaDependencies), false, ['scenario'=>'edit'])];

                case 'cc':
                    $dependencyDetails->setOutputAsObject(false);
                    return[$mapping[$key] => $dependencyDetails->getDependencyDetails($key, $value)];

                default:
                    return null;
            }
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Looping the value to save Recure Model
     * @return \Response
     */
    public function recurSave($recur, $ticket, $model, $request)
    {
        $fill = [];
        foreach ($recur as $key => $value) {
            if ((($key == 'end_date') || ($key == 'execution_time')) && !$value) {
                $value = null;
            }
            $fill[$key] = $value;
        }
        $panel = (strpos(URL::previous(), 'agent') !== false) ? 'agent_panel' : 'admin_panel';
        $fill = array_merge($fill, ['user_id' => Auth::user()->id, 'type' => $panel]);
        $model->fill($fill)->save();
        $files = $request->file();
        if (count($ticket) > 0) {
            foreach ($ticket as $key => $value) {
                RecureContent::updateOrCreate(
                    ['recur_id' => $model->id, 'option' => $key],
                    ['value' => $value,'recur_id' => $model->id, 'option' => $key]
                );
            }
            $updatedKeys = array_keys($ticket);
            // remove the fields that is not there in the request
            RecureContent::whereNotIn('option', $updatedKeys)
              ->where('recur_id', $model->id)->delete();
        }
        $this->updateLastExecutionOnSave($model);
        return $model;
    }

    /**
     * Return the Json response
     * @param string $message
     * @param int $code
     * @return \Response
     */
    public function jsonResponse($message, $code)
    {
        $m = ['message' => [$message]];
        if ($code != 200) {
            $m = ['error' => [$message]];
        }
        return response()->json($m, $code);
    }
    /**
     * create the ticket via execution
     * @return vide
     */
    public function recur()
    {
        try {
            $recur_ids = $this->execution();
            if (count($recur_ids) > 0) {
                foreach ($recur_ids as $id) {
                    $this->createTicket($id);
                    $this->updateRecur($id);
                }
            }
        } catch (\Exception $e) {
            loging('recurring', $e->getMessage() . ' Line=>' . $e->getLine() . " File=>" . $e->getFile());
        }
    }
    /**
     * Update the Recur model with executuion time
     * @param int $id
     */
    public function updateRecur($id)
    {
        Recur::updateOrCreate(['id' => $id], [
            'last_execution' => \Carbon\Carbon::now(),
        ]);
    }
    /**
     * Initaiate the execution
     * @return array
     */
    public function execution()
    {
        $now   = \Carbon\Carbon::now();
        $recur = Recur::select('id', 'interval', 'delivery_on', 'last_execution')
                ->whereDate('start_date', '<=', $now)
                ->where(function ($query) use ($now) {
                    $query->whereDate('end_date', '>=', $now)
                    ->orWhereNull('end_date');
                })
                ->get();
        $ids = $this->checking($recur);
        return $ids;
    }
    /**
     * Get the ids of the recure model
     * @param \Collection $recur
     * @return array
     */
    public function checking($recur)
    {
        $id = [];
        if ($recur->count() > 0) {
            foreach ($recur as $item) {
                $id[] = $this->getIdToExecute($item);
            }
        }
        return array_filter($id);
    }
    /**
     * Get the execution period
     * @param string $item
     * @return boolean
     */
    public function getIdToExecute($item)
    {
        $check = 0;
        $item->last_execution = $item->last_execution ? Carbon::parse($item->last_execution) : 0;
        switch ($item->interval) {
            case "daily":
                if (!$item->last_execution || !$item->last_execution->isToday()) {
                    $check = $item->id;
                }
                break;
            case "weekly":
                $check = $this->checkEventForDelivery((!$item->last_execution || $item->last_execution->isLastWeek()), $item, $check);
                break;
            case "monthly":
                $check = $this->checkEventForDelivery((!$item->last_execution || $item->last_execution->isLastMonth()), $item, $check);
                break;
            case "yearly":
                $check = $this->checkEventForDelivery((!$item->last_execution || $item->last_execution->isLastYear()), $item, $check);
                break;
        }
        return $check;
    }
    /**
     * Validate the input data
     * @param Request $request
     */
    public function validation($request)
    {
        $this->validate($request, $this->rules($request), $this->validateMessage());
    }
    /**
     * Rules to validate the input data
     * @param Request $request
     * @return array
     */
    public function rules($request)
    {
        $require = 'nullable';
        if ($request->has('recur.interval') && $request->input('recur.interval')
                != 'daily') {
            $require = 'required';
        }
        $rules = [
            'recur.start_date'  => 'required',
            'recur.interval'    => 'required',
            'recur.end_date'    => 'required_if:end,by',
            'recur.delivery_on' => $require
        ];
        return $rules;
    }
    /**
     * Message to to show validate fail message
     * @return array
     */
    public function validateMessage()
    {
        $message = [
            'recur.start_date.required'  => 'Start date required',
            'recur.interval.required'    => 'Interval required',
            'recur.end_date.required_if' => 'End date required',
            'recur.delivery_on.required' => 'Every required',
        ];
        return $message;
    }
    /**
     * Get the email controller class
     * @return \App\Http\Controllers\Common\PhpMailController
     */
    public function mailController()
    {
        return new \App\Http\Controllers\Common\PhpMailController();
    }
    /**
     * Deleting the recure
     * @param int $id
     * @return \Response
     */
    public function delete($id)
    {
        try {
            $recur = Recur::find($id);
            if ($recur) {
                $recur->delete();
            }
            return successResponse(Lang::get('lang.recur_ticket_delete_successfully'));
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }
    /**
     * Return the storage controller class
     * @return \App\FaveoStorage\Controllers\StorageController
     */
    public function storage()
    {
        return new \App\FaveoStorage\Controllers\StorageController();
    }
    /**
     * Rteunr extra files in an array
     * @param array $files
     * @param array $media
     * @return json
     */
    public function getFileArray($files, $media = array())
    {
        $files = checkArray('ticket', $files);
        //echo count($files) . PHP_EOL;
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_array($file)) {
                    foreach ($file as $attach) {
                        //echo $attach->getRealPath() . PHP_EOL;
                        $media[] = $this->storage()->saveObjectAttachments("", $attach, true);
                    }
                }
            }
        }
        return json_encode($media);
    }

    private function updateLastExecutionOnSave(Recur $model)
    {
        if (session()->get('lastExecNull')) {
            session()->forget('lastExecNull');
            $model->update(['last_execution' => null]);

            return $model;
        }

        return $model;
    }

    /**
     * Function to get current time in required format to check against delivery_on of event
     * @param  string  $interval  Time interval for executing recur events yearly/monthly/weekly
     * @return string             Current time to compare with delivery on time after \
     *                            checking time interval
     */
    private function getEventDeliveryTriggerCheck(string $interval): string
    {
        switch ($interval) {
            case 'monthly':
                return strtolower(Carbon::now()->format('d'));
            case 'yearly':
                return strtolower(Carbon::now()->format('F'));
            default:
                return strtolower(Carbon::now()->format('l'));
        }
    }

    /**
     * Function to check recur event delivery and last execution to decide event should execute or not
     * @param  bool   $lastExecutionCodition  Condition of last execution of the event
     * @param  object $item                   Single recur model object
     * @param  string $check                  initial value of event id to return if event does not satisfy conditions
     * @return string
     */
    private function checkEventForDelivery(bool $lastExecutionCodition, Recur $item, int $check): int
    {
        $eventDeliverOn = $this->getEventDeliveryTriggerCheck($item->interval);
        // dd($lastExecutionCodition, $eventDeliverOn, $item->delivery_on);
        if ($lastExecutionCodition && ($eventDeliverOn == $item->delivery_on)) {
            return $item->id;
        }

        return $check;
    }

    /**
     * Function to check recur permission and panel
     * @param $edit ( edit it true for edit api)
     * @return array [recur_permission, 'panel']
     */
    private function permissionAndPanelCheck($edit = false)
    {
        $this->recurPermission = User::has('recur_ticket');
        $relativeUrl = ($edit) ? str_replace(URL::to('/'), '', URL::previous()) : str_replace(URL::to('/'), '', \Request::url());
        $this->panel = (strpos($relativeUrl, 'agent') !== false) ? 'agent_panel' : 'admin_panel';
    }

    /**
     * Function to get minutes to use in delay while dispatching recur ticket creation 
     * job. This method returns the difference in current time(when command is being executed)
     * and execution time of recurring.Delayed dispatch of jobs creates an illusion that the
     * tickets are being created at the time of execution defined in recurring ticket. 
     *
     * Case 1: Execution time not defined in recur
     *         Simply return 0 as ticket can be created immediately after executing recur command
     * 
     * Case 2: Execution time is greater than current time
     *         Ticket should be created in future at execution time so simply return difference
     *         between current time and execution time and add in delay while dispatching jobs
     *
     * Case 3: Execution time is less than current time
     *         Ticket should have created before the recur command executed. But we are not the
     *         God of time so we will not try to time travel and create the ticket. Instead we
     *         will simply dispatch the job with delay to ensure the ticket is created on the
     *         next day at the execution time of recurring.
     *         Example
     *         Execution time : 11:00 AM
     *         Current time   : 12:00 PM
     *         In this case we will subtract the difference between execution and current time
     *         from 1440 minutes(24 hours). Here it will return 1380 minutes(23 hours) which will
     *         force job to create ticket at 11:00 on the next day.
     */
    private function getMinutesToDelayRecurExecution()
    {
        $executionTime = Recur::whereId($this->recure_id)->value('execution_time');
        if ($executionTime) {
            $current = now(timezone());
            $toExecute = Carbon::createFromFormat('H:i:s', $executionTime, timezone());
            $diff = $current->diffInMinutes($toExecute); //returns one minute less as considers seconds while calculating diff and picks floored value
            return ($toExecute->gt($current)) ? $diff : 1440 - $diff;
        }

        return 0;
    }
}
