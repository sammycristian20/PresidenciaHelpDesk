<?php

namespace App\Plugins\DepartmentStatusLink\Model;

use App\Model\helpdesk\Ticket\Ticket_Status as CoreTicketStatus;

class TicketStatus extends CoreTicketStatus
{
    /**
     * Get all of statuses attached with department
     */
    public function departments()
    {
        return $this->morphedByMany(Department::class, 'ticket_status_attachable', 'ticket_status_attachables', 'ticket_status_id', 'ticket_status_attachable_id');
    }
}
