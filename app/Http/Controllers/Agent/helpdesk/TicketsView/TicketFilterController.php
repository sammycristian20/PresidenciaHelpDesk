<?php

namespace App\Http\Controllers\Agent\helpdesk\TicketsView;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Ticket\TicketFilter;
use App\Model\helpdesk\Ticket\TicketFilterMeta;
use App\User;
use Auth;
use Cache;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Lang;
use Validator;
use App\Model\helpdesk\Form\FormField;
use App\Http\Controllers\Common\Dependency\DependencyDetails;

class TicketFilterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role.agent']);
    }

    /**
     * Get list of ticket filters of logged in user
     *
     * @return \Illuminate\Http\Response json
     */
    public function index()
    {
      try {
          $currentUser = Auth::user();

          $data = [];

          $accessibleFilters = TicketFilter::getAccessibleFilterIds();

          $data['own'] = TicketFilter::whereIn('id', $accessibleFilters['own_ids'])
            ->get(['id', 'name', 'icon_class']);

          $data['shared'] = TicketFilter::where('status', 1)
              ->whereIn('id', array_unique($accessibleFilters['shared_ids']))
              ->where('user_id', '<>', $currentUser->id)
              ->get(['id', 'name', 'icon_class']);

          return successResponse('', $data);
      } catch (Exception $e) {
          return errorResponse($e->getMessage());
      }
    }

    /**
     * Get ticket filter fields of logged in user
     *
     * @param $ticketFilter int Id of ticket filter
     * @return \Illuminate\Http\Response json
     */
    public function show($filterId)
    {

        try {

            // Check if ticket filter exists
            if (!TicketFilter::isGivenFilterAccessible($filterId)) {
                return errorResponse(Lang::get('lang.ticket-filter-not-found'), 404);
            }

            // Get current authenticated user
            $currentUser = Auth::user();

            $ticketFilter = TicketFilter::find($filterId);

            $ownFilter = $ticketFilter->user_id == $currentUser->id;

            $sharedFilter = $ticketFilter->whereHas('sharedUsers', function ($query) use ($currentUser) {
                $query->where([
                    ['ticket_filter_shareable_id', $currentUser->id],
                    ['ticket_filter_shareable_type', User::class],
                ])->orWhere(function ($query) use ($currentUser) {
                    $query->whereIn('ticket_filter_shareable_id', $currentUser->departments()
                            ->pluck('department.id')
                            ->toArray())
                        ->where('ticket_filter_shareable_type', Department::class);
                });
            })->get()->toArray();

            // Ticket filter data
            $data['id']         = $ticketFilter->id;
            $data['name']       = $ticketFilter->name;
            $data['created_at'] = $ticketFilter->created_at->toDateTimeString();
            $data['updated_at'] = $ticketFilter->updated_at->toDateTimeString();
            $data['display_on_dashboard'] = (bool)$ticketFilter->display_on_dashboard;
            $data['icon_class'] = $ticketFilter->icon_class;
            $data['icon_color'] = $ticketFilter->icon_color;

            // Is current user is the owner
            $data['is_shareable'] = $ownFilter;

            // Ticket filter fields
            $data['fields'] = $ticketFilter->filterMeta()->get(['key', 'value'])->toArray();


            $this->appendDependencyDetails($data['fields']);

            // Ticket filter shared with departments
            $data['departments'] = Department::whereHas('ticketFilterShares', function ($query) use ($ticketFilter) {
                $query->where('ticket_filter_id', $ticketFilter->id);
            })->get(['id', 'name']);

            // Ticket filter shared with agents
            $agents = User::whereHas('ticketFilterShares', function ($query) use ($ticketFilter) {
                $query->where('ticket_filter_id', $ticketFilter->id);
            })->get(['id', 'first_name', 'last_name', 'user_name']);

            $data['agents'] = $agents->map(function ($item) {
                return [
                    'id'   => $item->id,
                    'name' => $item->full_name,
                ];
            });

            return successResponse('', $data);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Appends dependency details to filter data
     * @param  array  $formFieldArray
     * @return null
     */
    private function appendDependencyDetails(array &$formFieldArray)
    {
      foreach ($formFieldArray as &$field) {

        if(!$this->isCustomField($field['key'])){

          $this->appendDefaultDependencyDetail($field);

        } else {

          $this->appendCustomDependencyDetail($field);
        }
      }
    }

    /**
     * Tells if a field is a custom field by its key
     * @param  string  $key  if `custom_` is the prefix then custom field else default
     * @return boolean
     */
    private function isCustomField(string $key)
    {
      return (strpos($key, 'custom_') !== false);
    }


    /**
     * Appends default dependency details
     * @param array $field
     * @return void
     */
    private function appendDefaultDependencyDetail(array &$field)
    {
      // check what all dependencies are available is dependency controller
      $apiDependencies = ['helptopic-ids', 'dept-ids', 'priority-ids', 'owner-ids',
        'assignee-ids', 'creator-ids', 'sla-plan-ids', 'team-ids', 'status-ids', 'type-ids',
        'source-ids', 'tag-ids', 'label-ids', 'location-ids', 'ticket-ids', 'organization-ids', 'collaborator-ids'];

      // append default fields
      // check what all dependencies are available is dependency controller
      // if default fields
      if(in_array($field['key'], $apiDependencies)){

        $field['value_meta'] = (new DependencyDetails)->getDependencyDetails($field['key'], $field['value']);

      } else {

        $booleanDependencies = ['assigned','answered','reopened', 'category', 'has-resolution-sla-met','has-response-sla-met','is-resolved'];

        if(in_array($field['key'], $booleanDependencies)){

          $field['value_meta'] = ['id'=>$field['value'], 'text'=> $this->getTextForNonDependencyFields($field['key'], $field['value'])];

        } else {

          if(is_string($field['value'])){
              // for handling cases where value contains special characters
              $field['value'] = urldecode($field['value']);
          }

          $field['value_meta'] = $field['value'];
        }
      }
    }

    /**
     * Gets text to be displayed on the filter for fields which are not there in dependency classes
     * For eg. category or answered
     * @return string
     */
    private function getTextForNonDependencyFields($key, $value)
    {
      if($key == 'category'){
        return Lang::get("lang.$value");
      }

      if($value == 1){
        return 'Yes';
      }

      return 'No';
    }

    /**
     * Appends custom field value to form field object
     * @param  array  $field
     * @return void
     */
    private function appendCustomDependencyDetail(array &$field)
    {
      // append custom fields
      $formFieldId = str_replace('custom_', '', $field['key']);

      $formField = FormField::where('id',$formFieldId)->select('id')->first();

      if($formField){
        $field['label'] = $formField->label;
      }
    }

    /**
     * Store a ticket filter of logged in user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response json
     */
    public function store(Request $request)
    {
        // Validate request data
        $this->validateInputs($request);

        try {
            // If update action
            if ($request->has('id') && $request->id != 0) {
                // Find ticket filter
                $ticketFilter = TicketFilter::find($request->id);

                // If ticket filter not found
                if (is_null($ticketFilter)) {
                    return errorResponse(Lang::get('lang.ticket-filter-not-found'));
                }

                $this->update($request, $ticketFilter);
            } else {
                // Get current authenticated user
                $currentUser = Auth::user();

                // Store ticket filter data
                $ticketFilter = $currentUser->ticketFilters()->create([
                    'name'   => $request->name,
                    'status' => 1,
                    'display_on_dashboard' => $request->display_on_dashboard,
                    'icon_class' => $request->icon_class,
                    'icon_color' => $request->icon_color,
                ]);

                // Store filter fields
                foreach ($request->fields as $field) {
                    $ticketFilter->filterMeta()->create([
                        'key'   => $field['key'],
                        'value' => $field['value'],
                    ]);
                }
            }

            // Store ticket filter sharing
            $this->saveTicketFilterSharing($request, $ticketFilter);

            return successResponse(Lang::get('lang.ticket-filter-save-successful'), $ticketFilter->first());
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TicketFilter  $ticketFilter
     * @return \Illuminate\Http\Response
     */
    public function destroy($ticketFilter)
    {
        try {
            // Find ticket filter
            $ticketFilter = TicketFilter::find($ticketFilter);

            // If ticket filter not found
            if (is_null($ticketFilter)) {
                return errorResponse(Lang::get('lang.ticket-filter-not-found'));
            }

            // Remove filter fields
            $ticketFilter->filterMeta()->delete();

            // Remove shared user if exists
            if ($ticketFilter->has('sharedUsers')) {
                $ticketFilter->sharedUsers()->detach();
            }

            // Remove shared department if exists
            if ($ticketFilter->has('sharedDepartments')) {
                $ticketFilter->sharedDepartments()->detach();
            }

            // Remove ticket filter
            $ticketFilter->delete();

            return successResponse(Lang::get('lang.ticket-filter-delete-successful'));
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TicketFilter  $ticketFilter
     * @return \Illuminate\Http\Response
     */
    protected function update(Request $request, TicketFilter $ticketFilter)
    {
        // Update ticket filter
        $ticketFilter->name = $request->name;
        $ticketFilter->display_on_dashboard = $request->display_on_dashboard;
        $ticketFilter->icon_class = $request->icon_class;
        $ticketFilter->icon_color = $request->icon_color;

        $ticketFilter->save();

        // remove all old filter key and values
        $ticketFilter->filterMeta()->delete();

        // Update filter fields
        foreach ($request->fields as $field) {
            $ticketFilterMeta = new TicketFilterMeta;
            $ticketFilterMeta->key   = $field['key'];
            $ticketFilterMeta->value = $field['value'];
            $ticketFilter->filterMeta()->save($ticketFilterMeta);
        }
    }

    /**
     * Store ticket filter sharing
     *
     * @param \Illuminate\Http\Request  $request
     * @return Void
     */
    private function saveTicketFilterSharing($request, $ticketFilter)
    {
        // share with agents
        if ($request->has('agents')) {
            // create and update entries for agents with whom filter is shared
            $ticketFilter->sharedUsers()->sync($request->agents);
        }

        // share with departments
        if ($request->has('departments')) {
            // create and update entries for departments with whom filter is shared
            $ticketFilter->sharedDepartments()->sync($request->departments);
        }
    }

    /**
     * Validate input parameters
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response If validation failed return error response
     */
    private function validateInputs($request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'fields'      => 'required|array',
            'id'          => 'sometimes|numeric',
            'agents'      => 'sometimes|array',
            'departments' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            $errors          = $validator->errors()->messages();
            $formattedErrors = array();

            foreach ($errors as $field => $message) {
                // if field is fields, it should send a 400 response with message
                // that a filter cannot be empty
                if($field == 'fields'){
                  $message = Lang::get('lang.invaild_filter');
                  throw new HttpResponseException(errorResponse($message, 400));
                }
                $formattedErrors[$field] = array_first($message);
            }

            throw new HttpResponseException(errorResponse($formattedErrors, 412));
        }
    }

    /**
     * function to return filter view page
     * @param $request
     * @return view
     */
    public function getFilterView() {
        try {
          return view('themes.default1.agent.helpdesk.ticket.tickets');
        } catch (Exception $e) {

            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * gets filter dependencies by its parameters.
     * @param  Request $request
     * @return Response
     */
    public function getFilterDependencies(Request $request)
    {
      // gets filter dependencies by its parameters.
      // for eg. if paramater is status-ids => [1], it will return the details of the status
      $params = $request->all();

      // map over all params and convert it into key, value
      $formFieldArray = [];

      foreach ($params as $key => $value) {
        $formFieldElement = [];
        $formFieldElement['key'] = $key;
        $formFieldElement['value'] = $value;
        array_push($formFieldArray, $formFieldElement);
      }
      $this->appendDependencyDetails($formFieldArray);

      return successResponse('', ['fields' => $formFieldArray]);
    }
}
