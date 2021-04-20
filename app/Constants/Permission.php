<?php

namespace App\Constants;

/**
 * Class Permission
 * It contain's all Helpdesk Permission constants
 * It will be useful for developers, when they want to add any new permission
 * they can look in this file, or suggestions will come while picking particular permission
 */
class Permission
{
    public const CREATETICKET = 'create_ticket';
    public const EDITTICKET = 'edit_ticket';
    public const CLOSETICKET = 'close_ticket';
    public const TRANSFERTICKET = 'transfer_ticket';
    public const DELETETICKET = 'delete_ticket';
    public const ASSIGNTICKET = 'assign_ticket';
    public const VIEWUNAPPROVEDTICKETS = 'view_unapproved_tickets';
    public const APPLYAPPROVALWORKFLOW = 'apply_approval_workflow';
    public const ACCESSKB = 'access_kb';
    public const REPORT = 'report';
    public const ORGANIZATIONDOCUMENTUPLOAD = 'organisation_document_upload';
    public const CHANGEDUEDATE = 'change_duedate';
    public const REASSIGNINGTICKETS = 're_assigning_tickets';
    public const GLOBALACCESS = 'global_access';
    public const RESTRICTEDACCESS = 'restricted_access';
    public const ACCESSUSERPROFILE = 'access_user_profile';
    public const ACCESSORGANIZATIONPROFILE = 'access_organization_profile';
    public const RECURTICKET = 'recur_ticket';
    public const USERACTIVATIONDEACTIVATION = 'user_activation_deactivation';
    public const AGENTACTIVATIONDEACTIVATION = 'agent_activation_deactivation';
    public const DELETEUSERACCOUNT = 'delete_user_account';
    public const DELETEAGENTACCOUNT = 'delete_agent_account';
}
