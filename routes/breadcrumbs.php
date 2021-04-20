<?php

Breadcrumbs::register('dashboard', function ($breadcrumbs) {
    //$breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.dashboard'), route('dashboard'));
});

Breadcrumbs::register('readmails', function ($breadcrumbs) {
    //$breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.emails'), route('readmails'));
});

Breadcrumbs::register('notification.list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('All Notifications', route('notification.list'));
});

Breadcrumbs::register('notification.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push('Notifications Settings', route('notification.settings'));
});

Breadcrumbs::register('groups.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.groups'), route('groups.index'));
});
Breadcrumbs::register('groups.create', function ($breadcrumbs) {
    $breadcrumbs->parent('groups.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('groups.create'));
});
Breadcrumbs::register('groups.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('groups.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('groups/{groups}/edit'));
});

Breadcrumbs::register('departments.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.departments'), route('departments.index'));
});
Breadcrumbs::register('departments.create', function ($breadcrumbs) {
    $breadcrumbs->parent('departments.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('departments.create'));
});
Breadcrumbs::register('departments.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('departments.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('departments/{departments}/edit'));
});
Breadcrumbs::register('department.profile.show', function ($breadcrumbs) {
    $breadcrumbs->parent('departments.index');
    $breadcrumbs->push(Lang::get('lang.view'), url('department.profile.show'));
});

Breadcrumbs::register('teams.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.teams'), route('teams.index'));
});
Breadcrumbs::register('teams.create', function ($breadcrumbs) {
    $breadcrumbs->parent('teams.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('teams.create'));
});
Breadcrumbs::register('teams.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('teams.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('teams/{teams}/edit'));
});
Breadcrumbs::register('teams.profile.show', function ($breadcrumbs) {
    $breadcrumbs->parent('teams.index');
    $breadcrumbs->push(Lang::get('lang.view'),  url('/assign-teams/{id}'));
});

Breadcrumbs::register('agents.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.agents'), route('agents.index'));
});
Breadcrumbs::register('agents.create', function ($breadcrumbs) {
    $breadcrumbs->parent('agents.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('agents.create'));
});
Breadcrumbs::register('agents.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('agents.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('agents/{agents}/edit'));
});

Breadcrumbs::register('user.show', function ($breadcrumbs, $id) {
    $user = App\User::select('role')->where('id', $id)->first();
    if ($user->role != 'user') {
        //checking previous page URL it may be  user or agent directory 

        if (strpos(url()->previous(), 'user') !== false) {
           $breadcrumbs->parent('user.index');
        }
        else{
           $breadcrumbs->parent('agents.index');
        }
        
    $breadcrumbs->push(Lang::get('lang.viewing_as_user_profile'), url('user/{user}'));

    } else {
        $breadcrumbs->parent('user.index');
        $breadcrumbs->push(Lang::get('lang.view_user_profile'), url('user/{user}'));
    }
});


Breadcrumbs::register('emails.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.emails'), route('emails.index'));
});
Breadcrumbs::register('emails.create', function ($breadcrumbs) {
    $breadcrumbs->parent('emails.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('emails.create'));
});
Breadcrumbs::register('emails.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('emails.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('emails/{emails}/edit'));
});

Breadcrumbs::register('banlist.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.banlists'), route('banlist.index'));
});
Breadcrumbs::register('banlist.create', function ($breadcrumbs) {
    $breadcrumbs->parent('banlist.index');
    $breadcrumbs->push(Lang::get('lang.add'), route('banlist.create'));
});
Breadcrumbs::register('banlist.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('banlist.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('agents/{agents}/edit'));
});

Breadcrumbs::register('template-sets.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push('All Template sets', route('template-sets.index'));
});
Breadcrumbs::register('show.templates', function ($breadcrumbs, $id) {
    $page = App\Model\Common\TemplateSet::whereId($id)->first();
    $breadcrumbs->parent('template-sets.index');
    $breadcrumbs->push($page->name, route('show.templates', $page->id));
});
Breadcrumbs::register('templates.edit', function ($breadcrumbs, $id) {
    $page = App\Model\Common\Template::select('templates.id as id', 'templates.set_id as set_id', 'template_types.name as name')
        ->join('template_types', 'templates.type', '=', 'template_types.id')->where('templates.id', '=', $id)->first();
    $breadcrumbs->parent('show.templates', $page->set_id);
    $breadcrumbs->push(\Lang::get('lang.edit-template', ['template_name' => $page->name]), route('templates.edit', $page->id));
});

Breadcrumbs::register('getdiagno', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.email_diagnostic'), route('getdiagno'));
});

Breadcrumbs::register('helptopic.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.help_topics'), route('helptopic.index'));
});
Breadcrumbs::register('helptopic.create', function ($breadcrumbs) {
    $breadcrumbs->parent('helptopic.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('helptopic.create'));
});
Breadcrumbs::register('helptopic.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('helptopic.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('helptopic/{helptopic}/edit'));
});

Breadcrumbs::register('sla.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.sla-plans'), route('sla.index'));
});
Breadcrumbs::register('sla.create', function ($breadcrumbs) {
    $breadcrumbs->parent('sla.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('sla.create'));
});
Breadcrumbs::register('sla.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('sla.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('sla/{sla}/edit'));
});
//requester form
Breadcrumbs::register('form.user', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.forms'), route('form.user'));
});
//ticket form

Breadcrumbs::register('forms.create', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.forms'), route('forms.create'));
});
//form group

Breadcrumbs::register('form-groups', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.forms'), url('/form-groups'));
});

//create
Breadcrumbs::register('form-group/create', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.forms'), url('/form-groups'));
    $breadcrumbs->push(Lang::get('lang.create'), url('/form-group/create'));
});

//edit
Breadcrumbs::register('form-group/edit', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.forms'), url('/form-groups'));
    $breadcrumbs->push(Lang::get('lang.edit'), url('/form-group/edit/{groupId}'));
});

Breadcrumbs::register('get.job.scheder', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.cron-jobs'), route('get.job.scheder'));
});

Breadcrumbs::register('getcompany', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.company_settings'), route('getcompany'));
});

Breadcrumbs::register('recaptcha', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.recaptcha'), route('recaptcha'));
});

Breadcrumbs::register('approval.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.approval_settings'), route('approval.settings'));
});

Breadcrumbs::register('getsystem', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.system-settings'), route('getsystem'));
});
Breadcrumbs::register('getticket', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.ticket-setting'), route('getticket'));
});
Breadcrumbs::register('getemail', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.email-settings'), route('getemail'));
});

Breadcrumbs::register('getalert', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.alert_notices_setitngs'), route('getalert'));
});
Breadcrumbs::register('security.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.security_settings'), route('security.index'));
});
// Templates > Upload Templates
Breadcrumbs::register('security.create', function ($breadcrumbs) {
    $breadcrumbs->parent('security.index');
    $breadcrumbs->push('Upload security', route('security.create'));
});
// Templates > [Templates Name]
Breadcrumbs::register('security.show', function ($breadcrumbs, $photo) {
    $breadcrumbs->parent('security.index');
    $breadcrumbs->push($photo->title, route('security.show', $photo->id));
});
// Templates > [Templates Name] > Edit Templates
Breadcrumbs::register('security.edit', function ($breadcrumbs, $photo) {
    $breadcrumbs->parent('security.show', $photo);
    $breadcrumbs->push('Edit security', route('security.edit', $photo->id));
});

Breadcrumbs::register('close-workflow.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.close_ticket_workflow_settings'), route('close-workflow.index'));
});

Breadcrumbs::register('statuss.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.status_settings'), route('statuss.index'));
});

Breadcrumbs::register('statuss.create', function ($breadcrumbs) {
    $breadcrumbs->parent('statuss.index');
    $breadcrumbs->push('Create Status', route('statuss.create'));
});

Breadcrumbs::register('status.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('statuss.index');
    $breadcrumbs->push('Edit Status', url('status/edit/{id}'));
});

Breadcrumbs::register('ratings.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.ratings_settings'), route('ratings.index'));
});

Breadcrumbs::register('rating.create', function ($breadcrumbs) {
    $breadcrumbs->parent('ratings.index');
    $breadcrumbs->push('Create Ratings', route('rating.create'));
});

Breadcrumbs::register('rating.edit', function ($breadcrumbs) {
    $page = App\Model\helpdesk\Ratings\Rating::whereId(1)->first();
    $breadcrumbs->parent('ratings.index');
    $breadcrumbs->push('Edit Ratings');
});

Breadcrumbs::register('admin-profile', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.profile'), route('admin-profile'));
});

Breadcrumbs::register('widgets', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.widget-settings'), route('widgets'));
});

Breadcrumbs::register('social.buttons', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.social-widget-settings'), route('social.buttons'));
});

Breadcrumbs::register('setting', function ($breadcrumbs) {
    $breadcrumbs->push(Lang::get('lang.admin_panel'), route('setting'));
});

Breadcrumbs::register('plugins', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.plugins'), route('plugins'));
});

Breadcrumbs::register('modules', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.modules'), route('modules'));
});

Breadcrumbs::register('LanguageController', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.language-settings'), route('LanguageController'));
});

Breadcrumbs::register('add-language', function ($breadcrumbs) {
    $breadcrumbs->parent('LanguageController');
    $breadcrumbs->push(Lang::get('lang.add'), route('add-language'));
});
Breadcrumbs::register('approval.workflow.show.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.approval-workflow'), route('approval.workflow.show.index'));
});
Breadcrumbs::register('approval.workflow.create', function ($breadcrumbs) {
    $breadcrumbs->parent('approval.workflow.show.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('approval.workflow.create'));
});
Breadcrumbs::register('approval.workflow.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('approval.workflow.show.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('approval-workflow/{id}/edit'));
});
Breadcrumbs::register('workflow', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.ticket_workflow'), route('workflow'));
});
Breadcrumbs::register('workflow.create', function ($breadcrumbs) {
    $breadcrumbs->parent('workflow');
    $breadcrumbs->push(Lang::get('lang.create'), route('workflow.create'));
});

Breadcrumbs::register('workflow.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('workflow');
    $breadcrumbs->push(Lang::get('lang.edit'), url('workflow/edit/{id}'));
});
Breadcrumbs::register('system-backup', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.system_backup'), route('system-backup'));
});
Breadcrumbs::register('api.settings.get', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.api_settings'), route('api.settings.get'));
});

Breadcrumbs::register('err.debug.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.error-debug-settings'), route('err.debug.settings'));
});

Breadcrumbs::register('closed.approvel.ticket', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.approvel_ticket_list'), route('closed.approvel.ticket'));
});
Breadcrumbs::register('user.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.user_directory'), route('user.index'));
});
Breadcrumbs::register('user.create', function ($breadcrumbs) {
    $breadcrumbs->parent('user.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('user.create'));
});
Breadcrumbs::register('user.edit', function ($breadcrumbs, $id) {
    $user = App\User::select('role')->where('id', '=', $id)->first();
    if ($user->role == 'user') {
        $breadcrumbs->parent('user.index');
    } else {
        $breadcrumbs->parent('agents.index');
    }

    $breadcrumbs->push(Lang::get('lang.edit'), url('user/{user}/edit'));
});

Breadcrumbs::register('user.invoic.page', function ($breadcrumbs) {
    //$breadcrumbs->parent('user.index');

    $breadcrumbs->push(Lang::get('Bill::lang.user_invoice'), url('bill/package/user-invoice/{id}'));
});

Breadcrumbs::register('order.info', function ($breadcrumbs) {
   // $breadcrumbs->parent('user.index');

    $breadcrumbs->push(Lang::get('Bill::lang.user_order'), url('bill/order/{orderId}'));
});


Breadcrumbs::register('agent.show', function ($breadcrumbs) {
   //checking previous page URL it may be  user or agent directory 
    if (strpos(url()->previous(), 'user') !== false) {
           $breadcrumbs->parent('user.index');
        }
    else{
           $breadcrumbs->parent('agents.index');
        }
   $breadcrumbs->push(Lang::get('lang.viewing_as_agent_profile'), url('agent/{id}'));
});

Breadcrumbs::register('user.export', function ($breadcrumbs) {
    $breadcrumbs->parent('user.index');
    $breadcrumbs->push("Export", url('user-export'));
});

Breadcrumbs::register('organizations.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.organization_directory'), route('organizations.index'));
});
Breadcrumbs::register('organizations.create', function ($breadcrumbs) {
    $breadcrumbs->parent('organizations.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('organizations.create'));
});
Breadcrumbs::register('organizations.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('organizations.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('organizations/{organizations}/edit'));
});
Breadcrumbs::register('organizations.show', function ($breadcrumbs) {
    $breadcrumbs->parent('organizations.index');
    $breadcrumbs->push(Lang::get('lang.view_organization_profile'), url('organizations/{organizations}'));
});
Breadcrumbs::register('canned.list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.canned_response'), route('canned.list'));
});
Breadcrumbs::register('canned.create', function ($breadcrumbs) {
    $breadcrumbs->parent('canned.list');
    $breadcrumbs->push(Lang::get('lang.create'), route('canned.create'));
});

Breadcrumbs::register('canned.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('canned.list');
    $breadcrumbs->push(Lang::get('lang.edit'), url('canned/edit/{id}'));
});

Breadcrumbs::register('tools.recure.list', function($breadcrumbs){
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.ticket_recurring'), route('tools.recure.list'));
});
Breadcrumbs::register('tools.recure.get', function($breadcrumbs){
    $breadcrumbs->parent('tools.recure.list');
    $breadcrumbs->push(Lang::get('lang.create_recurring'), route('tools.recure.get'));
});

Breadcrumbs::register('tools.recure.edit', function($breadcrumbs,$id){
    $breadcrumbs->parent('tools.recure.list');
    $breadcrumbs->push(Lang::get('lang.edit_recurring'), route('tools.recure.edit',['id'=>$id]));
});

Breadcrumbs::register('profile', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.my_profile'), route('profile'));
});
Breadcrumbs::register('agent-profile-edit', function ($breadcrumbs) {
    $breadcrumbs->parent('profile');
    $breadcrumbs->push(Lang::get('lang.edit'), url('profile-edit'));
});
Breadcrumbs::register('inbox.ticket', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets') . '&nbsp; > &nbsp;' . Lang::get('lang.inbox'), route('inbox.ticket'));
});
Breadcrumbs::register('open.ticket', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets') . '&nbsp; > &nbsp;' . Lang::get('lang.open'), route('open.ticket'));
});
Breadcrumbs::register('answered.ticket', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets') . '&nbsp; > &nbsp;' . Lang::get('lang.answered'), route('answered.ticket'));
});
Breadcrumbs::register('myticket.ticket', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets') . '&nbsp; > &nbsp;' . Lang::get('lang.my_tickets'), route('myticket.ticket'));
});
Breadcrumbs::register('overdue.ticket', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets') . '&nbsp; > &nbsp;' . Lang::get('lang.overdue'), route('overdue.ticket'));
});
Breadcrumbs::register('closed.ticket', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets') . '&nbsp; > &nbsp;' . Lang::get('lang.closed'), route('closed.ticket'));
});
Breadcrumbs::register('assigned.ticket', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets') . '&nbsp; > &nbsp;' . Lang::get('lang.assigned'), route('assigned.ticket'));
});
Breadcrumbs::register('newticket', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');

    $breadcrumbs->push(Lang::get('lang.tickets') . '&nbsp; > &nbsp;' . Lang::get('lang.create'), route('newticket'));
});
Breadcrumbs::register('ticket.thread', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('dashboard');
    $ticketInfo = App\Model\helpdesk\Ticket\Tickets::where('id',$id)->first();
    $breadcrumbs->push(Lang::get('lang.tickets'));
    $breadcrumbs->push($ticketInfo->ticket_number, route('ticket.thread',$ticketInfo->id));
});
Breadcrumbs::register('get-trash', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets') . '&nbsp; > &nbsp;' . Lang::get('lang.trash'), route('get-trash'));
});
Breadcrumbs::register('unassigned', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets') . '&nbsp; > &nbsp;' . Lang::get('lang.unassigned'), route('unassigned'));
});

Breadcrumbs::register('dept.open.ticket', function ($breadcrumbs, $dept) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.department') . '&nbsp; > &nbsp;' . $dept . '&nbsp; > &nbsp;' . Lang::get('lang.open_tickets'), url('/{dept}/open'));
});
Breadcrumbs::register('dept.closed.ticket', function ($breadcrumbs, $dept) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.department') . '&nbsp; > &nbsp;' . $dept . '&nbsp; > &nbsp;' . Lang::get('lang.closed_tickets'), url('/{dept}/closed'));
});
Breadcrumbs::register('report.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.dashboard'), route('dashboard'));
});
Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
});
Breadcrumbs::register('/', function ($breadcrumbs) {
    $breadcrumbs->push(Lang::get('lang.home'), route('/'));
});
Breadcrumbs::register('form', function ($breadcrumbs) {
    $breadcrumbs->push(Lang::get('lang.create_ticket'), route('form'));
});
Breadcrumbs::register('check_ticket', function ($breadcrumbs, $id) {
    $page = \App\Model\helpdesk\Ticket\Tickets::whereId(1)->first();
    $breadcrumbs->parent('ticket2');
    $breadcrumbs->push(Lang::get('lang.check_ticket'));
});
Breadcrumbs::register('show.ticket', function ($breadcrumbs) {
    $breadcrumbs->push('Ticket', route('form'));
});
Breadcrumbs::register('client.profile', function ($breadcrumbs) {
    $breadcrumbs->push('My Profile', route('client.profile'));
});
Breadcrumbs::register('ticket2', function ($breadcrumbs) {
    $breadcrumbs->push(Lang::get('lang.my_tickets'), route('ticket2'));
});

// Breadcrumbs::register('client-verify-number', function ($breadcrumbs) {
//     $breadcrumbs->push('Profile', route('client-verify-number'));
// });
// Breadcrumbs::register('post-client-verify-number', function ($breadcrumbs) {
//     $breadcrumbs->push('My Profile', route('post-client-verify-number'));
// });
Breadcrumbs::register('error500', function ($breadcrumbs) {
    $breadcrumbs->push('500');
});
Breadcrumbs::register('error404', function ($breadcrumbs) {
    $breadcrumbs->push('404');
});
Breadcrumbs::register('errordb', function ($breadcrumbs) {
    $breadcrumbs->push('Error establishing connection to database');
});
Breadcrumbs::register('unauth', function ($breadcrumbs) {
    $breadcrumbs->push('Unauthorized Access');
});
Breadcrumbs::register('board.offline', function ($breadcrumbs) {
    $breadcrumbs->push('Board Offline');
});
Breadcrumbs::register('category.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.category'), route('category.index'));
});
Breadcrumbs::register('category.create', function ($breadcrumbs) {
    $breadcrumbs->parent('category.index');
    $breadcrumbs->push(Lang::get('lang.add'), route('category.create'));
});
Breadcrumbs::register('category.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('category.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('category/{category}/edit'));
});
Breadcrumbs::register('category.show', function ($breadcrumbs) {
    $breadcrumbs->parent('category.index');
    $breadcrumbs->push(Lang::get('lang.view'), url('category/{category}'));
});

Breadcrumbs::register('article.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.article'), route('article.index'));
});
Breadcrumbs::register('article.create', function ($breadcrumbs) {
    $breadcrumbs->parent('article.index');
    $breadcrumbs->push(Lang::get('lang.add'), route('article.create'));
});
Breadcrumbs::register('article.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('article.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('article/{article}/edit'));
});
Breadcrumbs::register('article.show', function ($breadcrumbs) {
    $breadcrumbs->parent('article.index');
    $breadcrumbs->push(Lang::get('lang.view'), url('article/{article}'));
});

Breadcrumbs::register('article.alltemplate.list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.article_template'), route('article.alltemplate.list'));
});
Breadcrumbs::register('article.create.template', function ($breadcrumbs) {
    $breadcrumbs->parent('article.alltemplate.list');
    $breadcrumbs->push(Lang::get('lang.add'), route('article.create.template'));
});
Breadcrumbs::register('article.edit.template', function ($breadcrumbs) {
     $breadcrumbs->parent('article.alltemplate.list');
    $breadcrumbs->push(Lang::get('lang.edit'), url('articletemplate/{id}/edit'));
});



Breadcrumbs::register('settings', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.settings'), route('settings'));
});
Breadcrumbs::register('comment', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.comments'), route('comment'));
});
Breadcrumbs::register('page.index', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.pages'), route('page.index'));
});
Breadcrumbs::register('page.create', function ($breadcrumbs) {
    $breadcrumbs->parent('page.index');
    $breadcrumbs->push(Lang::get('lang.add'), route('page.create'));
});
Breadcrumbs::register('page.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('page.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('page/{page}/edit'));
});
Breadcrumbs::register('page.show', function ($breadcrumbs) {
    $breadcrumbs->parent('page.index');
    $breadcrumbs->push(Lang::get('lang.view'), url('page/{page}'));
});
Breadcrumbs::register('article-list', function ($breadcrumbs) {
    $breadcrumbs->push('Article List', route('article-list'));
});

Breadcrumbs::register('search', function ($breadcrumbs) {
    $breadcrumbs->push('Knowledge-base', route('home'));
    $breadcrumbs->push('Search Result');
});

Breadcrumbs::register('show', function ($breadcrumbs) {
    $breadcrumbs->push('Knowledge-base', route('home'));
    $breadcrumbs->push('Article List', route('article-list'));
    $breadcrumbs->push('Article');
});
Breadcrumbs::register('category-list', function ($breadcrumbs) {
    $breadcrumbs->push('Category List', route('category-list'));
});
Breadcrumbs::register('categorylist', function ($breadcrumbs) {
    $breadcrumbs->push('Category List', route('category-list'));
    $breadcrumbs->push('Category');
});
Breadcrumbs::register('contact', function ($breadcrumbs) {
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.contact'), route('contact'));
});
Breadcrumbs::register('pages', function ($breadcrumbs) {
    $breadcrumbs->push('Pages');
});
Breadcrumbs::register('queue', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.queues'), route('queue'));
});
Breadcrumbs::register('queue.edit', function ($breadcrumbs) {
    $id = \Input::segment(2);
    $breadcrumbs->parent('queue');
    $breadcrumbs->push(Lang::get('lang.edit'), route('queue.edit', $id));
});

Breadcrumbs::register('url.settings', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.url'), route('url.settings'));
});

Breadcrumbs::register('social', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.social-media-login'), route('social'));
});
Breadcrumbs::register('social.media', function ($breadcrumbs) {
    $id = \Input::segment(2);
    $breadcrumbs->parent('social');
    $breadcrumbs->push(Lang::get('lang.settings'), route('social.media', $id));
});
Breadcrumbs::register('priority.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('Ticket Priority'), route('priority.index'));
});
Breadcrumbs::register('priority.create', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('Ticket Priority'), route('priority.index'));
    $breadcrumbs->push(Lang::get('lang.create'), route('priority.create'));
});
Breadcrumbs::register('priority.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('Ticket Priority'), route('priority.index'));
    $breadcrumbs->push(Lang::get('Edit'), route('priority.index'));
});

Breadcrumbs::register('dept.inprogress.ticket', function ($breadcrumbs, $dept) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.department') . '&nbsp; > &nbsp;' . $dept . '&nbsp; > &nbsp;' . Lang::get('lang.assigned_tickets'), url('/{dept}/inprogress'));
});

Breadcrumbs::register('labels.index', function($breadcrumbs){
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.labels'), route('labels.index'));
});

Breadcrumbs::register('labels.create', function($breadcrumbs){
    $breadcrumbs->parent('labels.index');
    $breadcrumbs->push(Lang::get('lang.create'),route('labels.create'));
});

Breadcrumbs::register('labels.edit', function($breadcrumbs){
    $breadcrumbs->parent('labels.index');
    $breadcrumbs->push(Lang::get('lang.edit'),url('labels/{labels}/edit'));
});


/**
 * Tags
 */
Breadcrumbs::register('tag', function($breadcrumbs){
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.tags'), route('tag'));
});
Breadcrumbs::register('tag.create', function($breadcrumbs){
    $breadcrumbs->parent('tag');
    $breadcrumbs->push(Lang::get('lang.create'), route('tag.create'));
});
Breadcrumbs::register('tag.edit', function($breadcrumbs,$id){
    $breadcrumbs->parent('tag');
    $breadcrumbs->push(Lang::get('lang.edit'), route('tag.edit',$id));
});

Breadcrumbs::register('org.list', function($breadcrumbs){
    $breadcrumbs->push('Agents list', 'org.list');
});

Breadcrumbs::register('dashboard-getdepartment', function($breadcrumbs){
    $breadcrumbs->push('Getting department data', 'dashboard-getdepartment');
});

Breadcrumbs::register('switch-user-lang', function ($breadcrumbs) {
    $breadcrumbs->push('switch-language', 'switch-user-lang');
});

Breadcrumbs::register('get_canned', function ($breadcrumbs) {
    $breadcrumbs->push('Canned response', 'get_canned');
});

Breadcrumbs::register('get-canned-shared-departments', function ($breadcrumbs) {
    $breadcrumbs->push('Canned response departments', 'get-canned-shared-departments');
});

Breadcrumbs::register('get-canned-message', function ($breadcrumbs) {
    $breadcrumbs->push('Canned response message', 'get-canned-message');
});

Breadcrumbs::register('tickets-view', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets'), 'tickets-view');
});

Breadcrumbs::register('api-get-department-names', function ($breadcrumbs) {
    $breadcrumbs->push('get departments', 'api-get-department-names');
});

Breadcrumbs::register('api-get-sla-plans', function ($breadcrumbs) {
    $breadcrumbs->push('get sla plans', 'api-get-sla-plans');
});

Breadcrumbs::register('api-get-priorities', function ($breadcrumbs) {
    $breadcrumbs->push('get priorities', 'api-get-priorities');
});

Breadcrumbs::register('api-get-lables', function ($breadcrumbs) {
    $breadcrumbs->push('get labels', 'api-get-lables');
});

Breadcrumbs::register('api-get-tags', function ($breadcrumbs) {
    $breadcrumbs->push('get tags', 'api-get-tags');
});

Breadcrumbs::register('api-get-sources', function ($breadcrumbs) {
    $breadcrumbs->push('get-sources', 'api-get-sources');
});

Breadcrumbs::register('api-get-owners', function ($breadcrumbs) {
    $breadcrumbs->push('get owners', 'api-get-owners');
});

Breadcrumbs::register('api-get-types', function ($breadcrumbs) {
    $breadcrumbs->push('get types', 'api-get-types');
});

Breadcrumbs::register('api-get-assignees', function ($breadcrumbs) {
    $breadcrumbs->push('get assignee teams and agents', 'api-get-assignees');
});

Breadcrumbs::register('api-get-status', function ($breadcrumbs) {
    $breadcrumbs->push('get ticket status', 'api-get-status');
});

Breadcrumbs::register('api-get-assignees-2', function ($breadcrumbs) {
    $breadcrumbs->push('get ticket agents', 'api-get-assignees-2');
});

Breadcrumbs::register('agent.list', function ($breadcrumbs) {
    $breadcrumbs->push('show agents list', 'agent.list');
});

Breadcrumbs::register('get-department-table-data', function ($breadcrumbs) {
    $breadcrumbs->push('get department table', 'get-department-table-data');
});

Breadcrumbs::register('get-team-table-data', function ($breadcrumbs) {
    $breadcrumbs->push('get team table', 'get-team-table-data');
});

Breadcrumbs::register('api-get-ticket-number', function ($breadcrumbs) {
    $breadcrumbs->push('get team table', 'api-get-ticket-number');
});

Breadcrumbs::register('clean-database', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.delete_dummy_data'), 'clean-database');
});

Breadcrumbs::register('password.email', function ($breadcrumbs) {
    $breadcrumbs->parent('/');
    $breadcrumbs->push('Login', url('auth/login'));
    $breadcrumbs->push('Forgot Password', url('password/email'));
});

Breadcrumbs::register('auth.register', function ($breadcrumbs) {
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.login'), url('auth/login'));
    $breadcrumbs->push(Lang::get('lang.create_account'), url('auth/register'));
});

Breadcrumbs::register('auth.login', function ($breadcrumbs) {
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.create_account'), url('auth/register'));
    $breadcrumbs->push(Lang::get('lang.login'), url('auth/login'));
});

Breadcrumbs::register('login', function ($breadcrumbs) {
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.login'), 'login');
});



Breadcrumbs::register('sla.business.hours.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.business_hours'));
});

Breadcrumbs::register('sla.business.hours.create', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.business_hours'), route('sla.business.hours.index'));
    $breadcrumbs->push(Lang::get('lang.create'));
});

Breadcrumbs::register('sla.business.hours.edit', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.business_hours'),route('sla.business.hours.index'));
    $breadcrumbs->push(Lang::get('lang.edit'));
});

Breadcrumbs::register('ticket.type.index', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.ticket_type'), route('ticket.type.index'));
});

Breadcrumbs::register('ticket.type.create', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.ticket_type'), route('ticket.type.index'));
    $breadcrumbs->push(Lang::get('lang.create'), route('ticket.type.create'));
});

Breadcrumbs::register('ticket.type.edit', function ($breadcrumbs) {
   $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.ticket_type'), route('ticket.type.index'));
    $breadcrumbs->push(Lang::get('Edit'), route('ticket.type.index'));
});

Breadcrumbs::register('users-options', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.user-options'), route('users-options'));
});



Breadcrumbs::register('notification', function ($breadcrumbs) {
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.notifications'), route('notification'));
});

Breadcrumbs::register('otp-verification', function ($breadcrumbs) {
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.verify-mobile'), route('otp-verification'));
});

Breadcrumbs::register('api-get-helptopics', function ($breadcrumbs) {
    $breadcrumbs->push('get helptopics', 'api-get-helptopics');
});

Breadcrumbs::register('email-verification', function ($breadcrumbs) {
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.verify-email'), route('email-verification'));
});

Breadcrumbs::register('resend-activation-link', function ($breadcrumbs) {
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.verify-email'), route('resend-activation-link'));
});

/**
 * Source
 */
Breadcrumbs::register('source', function($breadcrumbs){
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.source'), route('source'));
});
Breadcrumbs::register('source.create', function($breadcrumbs){
    $breadcrumbs->parent('source');
    $breadcrumbs->push(Lang::get('lang.create'), route('source.create'));
});
Breadcrumbs::register('source.edit', function($breadcrumbs,$id){
    $breadcrumbs->parent('source');
    $breadcrumbs->push(Lang::get('lang.edit'), route('source.edit',$id));
});


Breadcrumbs::register('ticket.details.edit', function($breadcrumbs,$id){
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.edit'), route('ticket.details.edit',$id));
});

Breadcrumbs::register('system.update', function($breadcrumbs,$id){
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.edit'), route('system.update',$id));
});
Breadcrumbs::register('ticket.recure.list', function($breadcrumbs){
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.ticket_recurring'), route('ticket.recure.list'));
});
Breadcrumbs::register('ticket.recure.get', function($breadcrumbs){
    $breadcrumbs->parent('ticket.recure.list');
    $breadcrumbs->push(Lang::get('lang.create_recurring'), route('ticket.recure.get'));
});

Breadcrumbs::register('ticket.recure.edit', function($breadcrumbs,$id){
    $breadcrumbs->parent('ticket.recure.list');
    $breadcrumbs->push(Lang::get('lang.edit_recurring'), route('ticket.recure.edit',['id'=>$id]));
});

Breadcrumbs::register('dashboard-statistics', function($breadcrumbs){
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.dashboard-statistics'), route('dashboard-statistics'));
});

Breadcrumbs::register('listener.index', function($breadcrumbs){
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.listeners'), route('listener.index'));
});
Breadcrumbs::register('listener.create', function($breadcrumbs){
    $breadcrumbs->parent('listener.index');
    $breadcrumbs->push(Lang::get('lang.create'), route('listener.create'));
});

Breadcrumbs::register('listener.edit', function($breadcrumbs,$id){
    $breadcrumbs->parent('listener.index');
    $breadcrumbs->push(Lang::get('lang.edit'), url('workflow/edit/{id}'));
});
Breadcrumbs::register('listener.show', function($breadcrumbs,$id){
    $breadcrumbs->parent('listener.index');
    $breadcrumbs->push(Lang::get('lang.show'), route('listener.show',['id'=>$id]));
});

/**
 * Webhook
 */
Breadcrumbs::register('webhook', function($breadcrumbs){
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.webhook'), route('webhook'));


//     /**
//  * Satellite Helpdesk
//  */
// Breadcrumbs::register('source', function($breadcrumbs){
//     $breadcrumbs->parent('setting');
//     $breadcrumbs->push(Lang::get('lang.source'), route('source'));
// });
// Breadcrumbs::register('source.create', function($breadcrumbs){
//     $breadcrumbs->parent('source');
//     $breadcrumbs->push(Lang::get('lang.create'), route('source.create'));
// });
// Breadcrumbs::register('source.edit', function($breadcrumbs,$id){
//     $breadcrumbs->parent('source');
//     $breadcrumbs->push(Lang::get('lang.edit'), route('source.edit',$id));
// });
});

Breadcrumbs::register('rating.feedback', function($breadcrumbs){
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.ratings'), route('rating.feedback', ['s', 's']));
});

Breadcrumbs::register('licenseError', function($breadcrumbs){
    $breadcrumbs->parent('/');
    $breadcrumbs->push(Lang::get('lang.licenseCode'), route('licenseError'));
});

// Filters
Breadcrumbs::register('filter', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(Lang::get('lang.tickets'), url('tickets/filter/{filterid}'));
});

//For Import Phaniraj K
Breadcrumbs::register('importer.form', function($breadcrumbs)
{
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(trans('lang.importer_upload'), route('importer.form'));
});

Breadcrumbs::register('importer.processing', function($breadcrumbs)
{
    $breadcrumbs->parent('importer.form');
    $breadcrumbs->push(trans('lang.importer_processing'), route('importer.processing'));
});

Breadcrumbs::register('websockets.view', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.websockets'), route('websockets.view'));
});

Breadcrumbs::register('settings.filesystems', function ($breadcrumbs) {
    $breadcrumbs->parent('setting');
    $breadcrumbs->push(Lang::get('lang.settings_file_system_page_header'), route('settings.filesystems'));
});
