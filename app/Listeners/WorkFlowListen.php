<?php

namespace App\Listeners;


use App\Events\Event;
use App\Events\Ticket\TicketCreating;
use App\Events\WorkFlowEvent;
use App\Model\helpdesk\Ticket\Tickets;
use App\Http\Controllers\Admin\helpdesk\Label\LabelController;
use App\Http\Controllers\Agent\helpdesk\Filter\TagController;
use App\Http\Requests\helpdesk\Ticket\TagsCreateFromInboxRequest;
use App\Model\helpdesk\Filters\Label;
use App\Model\helpdesk\Filters\Tag;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Model\helpdesk\Form\CustomFormValue;
use Illuminate\Http\Request;
use App\Http\Controllers\Agent\helpdesk\TicketsWrite\ApiApproverController;
use App\Model\helpdesk\Workflow\ApprovalWorkflow;

class WorkFlowListen
{
    /**
     * Handle the event.
     *
     * @param  WorkFlowListen  $event
     * @return void
     */
    public function handle(WorkFlowEvent $event)
    {
        $options          = $event->options['values'];
        $ticket           = $event->options['ticket'];
        $TicketController = new \App\Http\Controllers\Agent\helpdesk\TicketController();
        $workflow_ticket  = new \App\Http\Controllers\Common\TicketsWrite\TicketWorkflowController();
        event(new TicketCreating($options));
        $values           = $workflow_ticket->process($options);
        return $this->ticket($values, $ticket);
    }
    /**
     * update the ticket properties according to workflow
     * @param array $values
     * @param mixed $ticket
     * @return mixed
     */
    public function ticket($values, $ticket)
    {

        $custom = $this->getCustomValues($values);

        if (checkArray('dept_id', $values)) {
            $ticket->dept_id = (int) $values['dept_id'];
        }
        elseif (checkArray('department', $values)) {
            $ticket->dept_id = (int) $values['department'];
        }

        if (checkArray('help_topic_id', $values)) {
            $ticket->help_topic_id = $values['help_topic_id'];
        }
        if (checkArray('sla', $values)) {
            $ticket->sla        = $values['sla'];
        }
        if (checkArray('assigned_to', $values)) {
            $ticket->assigned_to = $values['assigned_to'];
            $ticket->team_id = null;
        } elseif (checkArray('agent', $values)) {
            $ticket->team_id = null;
            $ticket->assigned_to = $values['agent'];
        }

        if (checkArray('team_id', $values)) {

            $ticket->team_id = $values['team_id'];
            $ticket->assigned_to = null;
        }

        if (checkArray('priority_id', $values)) {
            $ticket->priority_id = $values['priority_id'];
        }
        elseif (checkArray('priority', $values)) {
            $ticket->priority_id = $values['priority'];
        }

        if (checkArray('type', $values)) {

            $ticket->type = $values['type'];
        }
        if (checkArray('source', $values)) {
            $ticket->source = $values['source'];
        }
        if (checkArray('status', $values)) {
            $ticket->status = $values['status'];
        }

        if (checkArray('location_id', $values)) {
            $ticket->location_id = $values['location_id'];
        }

        if (checkArray('user_id', $values)) {
            $ticket->user_id = $values['user_id'];
        }

        \Config::set('app.custom-fields', $custom);

        $ticket->save();

        // creating ticket thread only if fork is not set
        if (!$values['fork']) {
            (new TicketController)->ticketThread($values['subject'], $values['body'], $ticket->id, $values['user_id'], $values['attachment'], $values['inline'], $values['email_content']);
        }

        CustomFormValue::updateOrCreateCustomFields($custom, $ticket);

        $this->applyApprovalWorkflow($ticket, $values);

        $this->applyTags($ticket, $values);

        $this->applyLabels($ticket, $values);

        /**
         * This event can be used by plugins to perform actions based on the workflow. This event even can be used for
         * performing any action which happens after ticket creation
         * @param Tickets $ticket ticket instance
         * @param array $values request values after enforcing workflow
         */
        \Event::dispatch('workflow-enforced', [&$ticket, &$values]);

        return $ticket;
    }

    private function applyApprovalWorkflow($ticket, $values)
    {
        if(checkArray('approval_workflow_id', $values)) {
            $applyApprovalWorkflowId = $values['approval_workflow_id'];
            $ticketId = $ticket->id;
            $request = new Request;
            $request->merge(['ticket_id' => $ticketId, 'workflow_id' => $applyApprovalWorkflowId]);
            if((new \App\Model\helpdesk\Workflow\ApprovalWorkflow)->find($applyApprovalWorkflowId)) {
                (new \App\Http\Controllers\Agent\helpdesk\TicketsWrite\ApiApproverController)
                ->applyApprovalWorkflow($request, $values['approval_workflow_enforcer']." workflow");
            }
        }
    }

    /**
     * Applies tags on a ticket
     * @param $ticket
     * @param $values
     */
    private function applyTags($ticket, $values)
    {
        if(checkArray('tag_ids', $values)) {
            $ticketId = $ticket->id;
            $tags = Tag::whereIn('id', $values['tag_ids'])->pluck('name')->toArray();
            $request = new TagsCreateFromInboxRequest(['ticket_id' => $ticketId, 'tags' => $tags]);
            (new TagController)->addToFilter($request, false);
        }
    }

    /**
     * Applies labels on a ticket
     * @param $ticket
     * @param $values
     */
    private function applyLabels($ticket, $values)
    {
        if(checkArray('label_ids', $values)) {
            $ticketId = $ticket->id;
            $labels = Label::whereIn('id', $values['label_ids'])->pluck('title')->toArray();
            $request = new Request(['ticket_id' => $ticketId, 'labels' => $labels]);
            (new LabelController())->attachTicket($request, false);
        }
    }

    public function getCustomValues($values)
    {

        $keys   = ['email',
            'requester',
            'name',
            'rerquester_email',
            'requester_name',
            'subject',
            'body',
            'phone',
            'code',
            'mobile',
            'helptopic',
            'sla',
            'priority',
            'source',
            'cc',
            'department',
            'agent',
            //'team' => $team_assign,
            'status',
            //'custom_data'   => $form_data,
            //'auto_response' => $auto_response,
            'type',
            'attachment',
            'inline',
            'email_content',
            'organization', 'linkDept','user_requester', 'priority_id', 'dept_id', 'assigned_to','domain_id', 'company_name', 'org_dept'];
        $custom = array_except($values, $keys);
        return $custom;
    }
}
