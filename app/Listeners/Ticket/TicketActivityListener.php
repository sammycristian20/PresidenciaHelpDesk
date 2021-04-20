<?php


namespace App\Listeners\Ticket;

use App\Events\Ticket\TicketCreating;
use App\Events\Ticket\TicketForwarding;
use App\Events\Ticket\TicketListenerEnforcing;
use App\Events\Ticket\TicketSlaEnforcing;
use App\Events\Ticket\TicketThreadCreating;
use App\Events\Ticket\TicketThreadUpdating;
use App\Events\Ticket\TicketUpdating;
use App\Events\Ticket\TicketWorkflowEnforcing;
use App\Model\Common\TicketActivityLog;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Repositories\TicketActivityLogRepository;
use App\Traits\TicketKeyMutator;
use Illuminate\Events\Dispatcher;

/**
 * Listens for all ticket related events and update those in activity logs
 * @author Avinash Kumar <avinash.kumar@ladybirdweb.com>
 */
class TicketActivityListener
{
    use TicketKeyMutator;

    private $ticketLogRepository;

    public function __construct()
    {
        $this->ticketLogRepository = TicketActivityLogRepository::getInstance();
    }
    /**
     * Logs ticket creation process
     * @param TicketCreating $event
     */
    public function logTicketCreation(TicketCreating $event)
    {
        $this->ticketLogRepository->setActionTakerType($this->ticketLogRepository::USER);

        $this->ticketLogRepository->setLogs($this->ticketLogRepository::TICKET_CREATED, $event->ticketValueArray);
    }

    /**
     * Logs workflow enforcement process on a ticket
     * @param TicketWorkflowEnforcing $event
     */
    public function logWorkflowEnforcement(TicketWorkflowEnforcing $event)
    {
        $this->ticketLogRepository->setActionTakerType($this->ticketLogRepository::WORKFLOW);

        $this->ticketLogRepository->setActionTakerId($event->workflowId);

        $this->ticketLogRepository->setLogs($this->ticketLogRepository::WORKFLOW_ENFORCED, $event->ticketValueArray);
    }

    /**
     * Logs workflow enforcement process on a ticket
     * @param TicketUpdating $event
     */
    public function logTicketUpdation(TicketUpdating $event)
    {
        $this->ticketLogRepository->setActionTakerType($this->ticketLogRepository::USER);

        $this->ticketLogRepository->setLogs($this->ticketLogRepository::TICKET_UPDATED, $event->ticketValueArray);
    }

    /**
     * Logs listener enforcement process on a ticket
     * @param TicketListenerEnforcing $event
     */
    public function logListenerEnforcement(TicketListenerEnforcing $event)
    {
        $this->ticketLogRepository->setActionTakerType($this->ticketLogRepository::LISTENER);

        $this->ticketLogRepository->setActionTakerId($event->listenerId);

        $this->ticketLogRepository->setLogs($this->ticketLogRepository::LISTENER_ENFORCED, $event->ticketValueArray);
    }

    /**
     * Logs listener enforcement process on a ticket
     * @param TicketSlaEnforcing $event
     */
    public function logSlaEnforcement(TicketSlaEnforcing $event)
    {
        $this->ticketLogRepository->setActionTakerType($this->ticketLogRepository::SLA);

        $this->ticketLogRepository->setActionTakerId($event->slaId);

        $this->ticketLogRepository->setLogs($this->ticketLogRepository::SLA_ENFORCED, $event->ticketValueArray);
    }

    /**
     * Logs Thread creation related activities
     * @param TicketThreadCreating $event
     */
    public function logThreadCreation(TicketThreadCreating $event)
    {
        $threadValueArray = $event->threadValueArray;

        $this->ticketLogRepository->setActionTakerType($this->ticketLogRepository::USER);

        // todo: think if this logic is needed
        if (isset($threadValueArray['title'])) {
            // title is not a valid key but subject, but in ticket_thread table, title is the key
            $threadValueArray['subject'] = $threadValueArray['title'];
            unset($threadValueArray['title']);
        }

        // if there are no threads on the ticket, it must be the first thread, so must be considered under TICKET_CREATION
        if (isset($threadValueArray['ticket_id'])) {
            $isFirstThreadCreated = Ticket_Thread::where('ticket_id', $threadValueArray['ticket_id'])->where('is_internal', 0)->count();
            if (!$isFirstThreadCreated) {                
                // user_id key exists in ticket table also, hence it shuold not be considered as ticket change. Same goes for source column
                unset($threadValueArray['user_id'], $threadValueArray['source']);
                $this->ticketLogRepository->setLogs($this->ticketLogRepository::TICKET_CREATED, $threadValueArray);
            } else {
                $this->ticketLogRepository->setActionTakerId($threadValueArray['user_id'] ?? null);
                unset($threadValueArray['user_id'], $threadValueArray['source']);
                $eventType = (isset($threadValueArray['is_internal']) && $threadValueArray['is_internal']) ? $this->ticketLogRepository::INTERNAL_NOTE_ADDED : $this->ticketLogRepository::REPLY_ADDED ;

                $this->ticketLogRepository->setLogs($eventType, $threadValueArray);
            }

            TicketActivityLog::saveActivity($threadValueArray['ticket_id']);
        }
    }


    /**
     * Logs Thread updation related activities
     * @param TicketThreadCreating $event
     */
    public function logThreadUpdation(TicketThreadUpdating $event)
    {
        // threads only gets updated in case of subject change
        $threadValueArray = $event->threadValueArray;

        $this->ticketLogRepository->setActionTakerType($this->ticketLogRepository::USER);

        // user_id key exists in ticket table also, hence it shuold not be considered as ticket change
        unset($threadValueArray['user_id']);

        if (isset($threadValueArray['title'])) {
            // title is not a valid key but subject, but in ticket_thread table, title is the key
            $threadValueArray['subject'] = $threadValueArray['title'];
            unset($threadValueArray['title']);
        }
        $this->ticketLogRepository->setLogs($this->ticketLogRepository::TICKET_UPDATED, $threadValueArray);
    }

    public function logTicketForwarding(TicketForwarding $event)
    {

        $this->ticketLogRepository->setActionTakerType($this->ticketLogRepository::USER);

        $this->ticketLogRepository->setLogs($this->ticketLogRepository::TICKET_FORWARDED, [], ['value'=> $event->forwardedTo]);

        TicketActivityLog::saveActivity($event->ticketId);
    }

    /**
     * Registering listeners for all ticket related activities
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(TicketCreating::class, "\App\Listeners\Ticket\TicketActivityListener@logTicketCreation");

        $events->listen(TicketWorkflowEnforcing::class, "\App\Listeners\Ticket\TicketActivityListener@logWorkflowEnforcement");

        $events->listen(TicketUpdating::class, "\App\Listeners\Ticket\TicketActivityListener@logTicketUpdation");

        $events->listen(TicketListenerEnforcing::class, "\App\Listeners\Ticket\TicketActivityListener@logListenerEnforcement");

        $events->listen(TicketSlaEnforcing::class, "\App\Listeners\Ticket\TicketActivityListener@logSlaEnforcement");

        $events->listen(TicketThreadCreating::class, "\App\Listeners\Ticket\TicketActivityListener@logThreadCreation");

        $events->listen(TicketThreadUpdating::class, "\App\Listeners\Ticket\TicketActivityListener@logThreadUpdation");

        $events->listen(TicketForwarding::class, "\App\Listeners\Ticket\TicketActivityListener@logTicketForwarding");
    }
}
