<?php

namespace App\Plugins\DepartmentStatusLink\Model;

use App\Model\helpdesk\Agent\Department as CoreDepartment;

class Department extends CoreDepartment
{
    /**
     * Get all of the statuses for the department
     */
    public function statuses()
    {
        return $this->morphToMany(TicketStatus::class, 'ticket_status_attachable', 'ticket_status_attachables', 'ticket_status_attachable_id', 'ticket_status_id');
    }
}
