<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Model\helpdesk\Settings\Company;
use Auth;
use App\User;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketsCategoryController;

class AgentLayout extends TicketsCategoryController{

    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $company;
    protected $users;
    protected $tickets;
    protected $department;
    protected $emails;

    /**
     * Create a new profile composer.
     *
     * @param
     * @return void
     */
    public function __construct(Company $company, User $users, Tickets $tickets, Department $department, Emails $emails, CommonSettings $common_settings) {
        $this->company = $company;
        $this->auth = Auth::user();
        $this->users = $users;
        $this->tickets = $tickets;
        $this->department = $department;
        $this->emails = $emails;
        $this->common_settings = $common_settings;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view) {

        // check if route is dashboard
        $layoutVariables = [
            'is_mail_conigured' => $this->getEmailConfig(),
            'dummy_installation' => $this->getDummyDataInstallation(),
            'company' => $this->company,
        ];

        // NOTE: can be removed after https://github.com/ladybirdweb/faveo-helpdesk-advance/pull/6473
        if(strpos(url()->current(), "dashboard-old-layout") !== false){
            $layoutVariables = array_merge($layoutVariables, [
                'myticket' => $this->myTicketsQuery(),
                'unassigned' => $this->unassignedQuery(),
                'tickets' => $this->inboxQuery(),
                'overdues' => $this->overdueTicketsQuery(),
                'due_today' => $this->dueTodayTicketsQuery(),
            ]);
        }

        $view->with($layoutVariables);
    }

    public function users() {
        return $this->users->select('id', 'profile_pic');
    }

    public function tickets() {
        return $this->tickets->select('id', 'ticket_number');
    }

    /**
     * @category function to check configured mails
     * @param null
     * @var $emails
     * @return boolean true/false
     */
    public function getEmailConfig() {
        $emailForIncoming = $this->emails->where('sending_status', '=', 1)->count();
        $emailForOutGoing = $this->emails->where('fetching_status', '=', 1)->count();
        if ($emailForIncoming >= 1 && $emailForOutGoing >= 1) {
            return true;
        }
        return false;
    }

    /**
     * @category function to check if dummy data is installed in the system or not
     * @param null
     * @return bool
     */
    public function getDummyDataInstallation()
    {
        return (bool) $this->common_settings->where('option_name', '=', 'dummy_data_installation')->value('status');
    }

}
