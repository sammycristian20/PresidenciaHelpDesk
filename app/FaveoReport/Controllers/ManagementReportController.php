<?php

namespace App\FaveoReport\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Model\helpdesk\Ticket\Tickets;
use Exception;

/**
 * handles management report specific operations
 */
class ManagementReportController extends BaseReportController
{

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->type = 'management_report';
    }

    /**
     * Available short codes in management report
     * NOTE: ":" means a varible which is static, @ means a variable which has dependencies
     * and can be changed dynamically
     *
     * @var array
     */
    public $availableShortCodes = [
        ':created_at',
        ':updated_at',
        '@last_status_change(statusName)',
        ':duedate',
        ':first_response_time',
        ':closed_at'
    ];

    /**
     * gets management report data
     * @param $reportId
     * @return Response
     * @throws \App\FaveoReport\Exceptions\VariableNotFoundException
     */
    public function getManagementReportData($reportId)
    {
        $this->setReportId($reportId, "management-report");

        $limit = $this->request->input('limit') ?: 10;

        $sortField = $this->request->input('sort-field') ?: 'updated_at';

        $sortOrder = $this->request->input('sort-order') ?: 'desc';

        // in case of paginate, it is not required, but when a request is generated dynamically,
        // we need to change the page number dynamically but in that case paginate looks for
        // non-attribute property which is difficult to set
        $page = $this->request->input('page') ?: 1;

        $baseQuery = $this->getBaseQueryForTickets($this->request, $this->reportId);

        // filter handling has to be done
        // filters should be exact same as
        $this->appendMetaTicketQuery($baseQuery);

        $this->setCurrentPage($page);

        $tickets = $baseQuery->select('tickets.*')
            ->orderBy('tickets.' . $sortField, $sortOrder)->paginate($limit);

        // loop over tickets and add things which cannot be added directly from query
        foreach ($tickets as &$ticket) {

            $this->handleTicketExtraFields($ticket);

            $this->formatSubject($ticket);

            $this->formatOrganizations($ticket);

            $this->makeHyperlinkValues($ticket);

            $this->formatCustomFields($ticket);
        }

        return successResponse('', $tickets);
    }

    /**
     * Formats ticket custom fields
     * @param Ticket $ticket
     * @return void
     */
    private function formatCustomFields(Tickets &$ticket): void
    {
        $customFieldValues = $ticket->customFieldValues;

        foreach ($customFieldValues as $customField) {

            $key = "custom_" . $customField->form_field_id;

            if (!$this->isColumnVisible($key)) {
                continue;
            }

            // formatting it in id, name format, so that it can be converted into hyperlink
            $ticket->$key = (object)[];

            $ticket->$key->name = is_array($customField->value) ? implode(',', $customField->value) : $customField->value;

            // this method format hyperlink into ticket->custom_id->name format,
            // we will format it into ticket->custom_id in following line after this
            $this->updateDependencyHyperlink($key, $ticket->$key->name, $ticket, $key, false);

            $ticket->$key = $ticket->$key->name;
        }
        unset($ticket->customFieldValues);
    }

    /**
     * If column of the given key is visible or not
     * @param string $key
     * @return bool
     */
    private function isColumnVisible(string $key): bool
    {
        return (bool)$this->visibleColumns->where('key', $key)->count();
    }

    /**
     * Makes values of the ticket fields clickable
     * @param Tickets $ticket
     * @return void
     * @throws Exception
     */
    private function makeHyperlinkValues(Tickets &$ticket)
    {
        $this->ticketHyperLink('ticket_number', $ticket, $ticket->ticket_number);

        $this->ticketHyperLink('subject', $ticket, $ticket->subject);

        $this->updateDependencyHyperlink('status-ids', $ticket->status, $ticket, 'statuses');

        $this->updateDependencyHyperlink('dept-ids', $ticket->dept_id, $ticket, 'department');

        $this->updateDependencyHyperlink('priority-ids', $ticket->priority_id, $ticket, 'priority');

        $this->updateDependencyHyperlink('type-ids', $ticket->type, $ticket, 'types');

        $this->updateDependencyHyperlink('location-ids', $ticket->location_id, $ticket, 'location');

        $this->updateDependencyHyperlink('helptopic-ids', $ticket->help_topic_id, $ticket, 'helptopic');

        $this->updateDependencyHyperlink('source-ids', $ticket->source, $ticket, 'sources');

        $this->updateUserHyperlink('assignee-ids', $ticket->assigned_to, $ticket, 'assigned');

        $this->updateDependencyHyperlink('team-ids', $ticket->team_id, $ticket, 'assignedTeam');

        $this->updateUserHyperlink('owner-ids', $ticket->user_id, $ticket, 'user');

        $this->updateUserHyperlink('creator-ids', $ticket->creator_id, $ticket, 'creator');

        $this->updateArrayHyperlink('label-ids', $ticket, 'labels');

        $this->updateArrayHyperlink('tag-ids', $ticket, 'tags');

        $this->convertRequiredBooleanToHumanReabable($ticket);
    }

    /**
     * Formats organizations in comma seperated way
     * @param Ticket $ticket
     * @return void
     */
    private function formatOrganizations(Tickets $ticket): void
    {
        $ticket->organizations = '';

        if ($ticket->user && $ticket->user->organizations) {
            // loop over organizations and make it comma seperated
            $ticket->organizations = implode(', ', $ticket->user->organizations->pluck('name')->toArray());

            unset($ticket->user->organizations);
        }
    }

    /**
     * Formats subject and makes it an hyperlink
     * @param Tickets $ticket
     * @return void
     */
    private function formatSubject(Tickets $ticket): void
    {
        $ticket->subject = isset($ticket->firstThread->title) ? $ticket->firstThread->title : '';

        $ticket->description = isset($ticket->firstThread->body) ? strip_tags($ticket->firstThread->body) : '';

        unset($ticket->firstThread);
    }

    /**
     * Appends fields to a ticket which are not obtainable directly through query
     * @param Tickets $ticket
     * @return null
     * @throws \App\FaveoReport\Exceptions\VariableNotFoundException
     */
    private function handleTicketExtraFields(Tickets &$ticket)
    {
        // adding time track
        $ticket->time_tracked = $ticket->totalTimeTracked();

        $this->appendCustomColumnsData($ticket);
    }

    /**
     * Converts 0/1 to no/yes for boolean fields
     * @return void
     */
    private function convertRequiredBooleanToHumanReabable(Tickets &$ticket)
    {
        $ticket->is_response_sla = $this->convertBooleanToHumanReadable($ticket->is_response_sla);

        $ticket->is_resolution_sla = $this->convertBooleanToHumanReadable($ticket->is_resolution_sla);

        $ticket->overdue = $this->convertBooleanToHumanReadable(Tickets::isOverdue($ticket->duedate));
    }

    /**
     * Appends additional query required by management report to add additional data
     * @param QueryBuilder &$baseQuery
     * @return void
     */
    private function appendMetaTicketQuery(QueryBuilder &$baseQuery)
    {
        $baseQuery = $baseQuery->with([
            'types:id,name',
            'creator:id,email,user_name,first_name,last_name',
            'department:id,name',
            'helptopic:id,topic as name',
            'location:id,title as name',
            'firstThread:id,ticket_id,title,body',
            'tags:tags.id,ticket_id,value as name',
            'labels:labels.id,ticket_id,value as name',
            'user:id,first_name,last_name,email,user_name',
            'user.organizations:organization.id,name',
            'customFieldValues:id,value,form_field_id,custom_id,custom_type',
        ]);
    }
}
