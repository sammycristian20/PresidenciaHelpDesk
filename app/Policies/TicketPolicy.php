<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Auth;

class TicketPolicy {

    /**
     * User whose permission is required
     * @var User
     */
    private $user;

    public function __construct($userId = null)
    {
        if($userId){
            $this->user = User::find($userId);
        }
    }

    use HandlesAuthorization;

    /**
     * check the permission for provided keys
     * @param string $key
     * @return boolean
     */
    public function has($key) {

        $user = $this->user ? : Auth::user();

        // NOTE FROM AVINASH: returning true because earlier it used to return true if user not logged in. Not sure why
        if(!$user){
            return true;
        }

        // NOTE FROM AVINASH: returning true because earlier it used to return true if user role is user. Not sure why
        if($user->role == "user" || $user->role == "admin"){
            return true;
        }
    
        return User::has($key);
    }

    /**
     * ticket create permission
     * @return boolean
     */
    public function create() {
        return $this->has('create_ticket');
    }

    /**
     * ticket edit permission
     * @return boolean
     */
    public function edit() {
        return $this->has('edit_ticket');
    }

    /**
     * ticket close permission
     * @return boolean
     */
    public function close() {
        return $this->has('close_ticket');
    }

    /**
     * ticket assign permission
     * @return boolean
     */
    public function assign() {
        return $this->has('assign_ticket');
    }

    /**
     * ticket transfer/change department permission
     * @return boolean
     */
    public function transfer() {
        return $this->has('transfer_ticket');
    }

    /**
     * ticket delete permission
     * @return boolean
     */
    public function delete() {
        return $this->has('delete_ticket');
    }

    /**
     * ban user permission
     * @return boolean
     */
    public function ban() {
        return $this->has('ban_email');
    }

    /**
     * access kb permission
     * @return boolean
     */
    public function kb() {
        return $this->has('access_kb');
    }

    public function orgUploadDoc() {
        return $this->has('organisation_document_upload');
    }

    /**
     * access report permission
     * @return boolean
     */
    public function report() {
        return $this->has('report');
    }

    /**
     * verify email permission
     * @return boolean
     */
    public function emailVerification() {
        return $this->has('email_verification');
    }

    /**
     * verify mobile permission
     * @return boolean
     */
    public function mobileVerification() {
        return $this->has('mobile_verification');
    }

    public function ChangePriority() {
        return $this->has('change_priority');
    }

    /**
     * activate account permission
     * @return boolean
     */
    public function accountActivation() {
        return $this->has('account_activate');
    }

    /**
     * activate agent account permission
     * @return boolean
     */
    public function agentAccountActivation() {
        return $this->has('agent_account_activate');
    }

    public function changeDuedate() {
        return $this->has('change_duedate');
    }

    public function globalAccess() {
        return $this->has('global_access');
    }

    public function reAssigningTickets() {
        return $this->has('re_assigning_tickets');
    }

    public function restrictedAccess() {
        return $this->has('restricted_access');
    }

    /**
     * Permission whether logged in user can view un-approved tickets
     * @return boolean
     */
    public function viewUnapprovedTickets(){
        return $this->has('view_unapproved_tickets');
    }


    /**
     * Approval workflow permission
     * @return boolean
     */
    public function applyApprovalWorkflow(){
        return $this->has('apply_approval_workflow');
    }


    /**
     * User profile Access permission
     * @return boolean
     */
    public function accessUserProfile(){
        return $this->has('access_user_profile');
    }



    /**
     * Organization profile access permission
     * @return boolean
     */
    public function accessOrganizationProfile(){
        return $this->has('access_organization_profile');
    }

    /**
     * Recur ticket access permission
     * @return boolean
     */
    public function accessRecurTicket(){
        return $this->has('recur_ticket');
    }
}
