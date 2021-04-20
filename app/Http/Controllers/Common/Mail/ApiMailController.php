<?php

namespace App\Http\Controllers\Common\Mail;

use App\Http\Controllers\Common\Mail\BaseMailController;
use App\Http\Requests\helpdesk\Mail\MailSettingsRequest;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Settings\Email;
use Lang;
use Illuminate\Http\Request;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Agent\Department;

/**
 * handles all API related operations except fetch mail
 * It is child class of BaseMailController
 *
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class ApiMailController extends BaseMailController
{

    public function __construct()
    {
        $this->middleware('role.admin');
    }


    /**
     * Gets emailSettings of the given id
     * @param $id        email database ID
     * return Response   emailSettings data on success else failure response
     */
    public function getGivenMailSettings($id)
    {
        $emailSettings = Emails::with('department','priority','helptopic')->find($id);

        if(!$emailSettings){
            return errorResponse(Lang::get('lang.not_found'));
        }

        //removing password field
        $emailSettings['password'] = '';
        $emailSettings['is_system_default'] = ( Email::first()->sys_email == $emailSettings->id ) ? true : false;

        unset($emailSettings['help_topic']); // as it already have 'helptopic'

        return successResponse('',$emailSettings);
    }

    /**
     * saves emails settings after validating it
     * @param MailSettingsRequest $request
     * @return Response         success message or success else failure
     */
    public function saveEmailSettings(MailSettingsRequest $request){

        //get data from request
        $emailSettings = $request->all();

        //if there is no default email in the DB, it should mark the mail as system default
        $isSystemDefault = Emails::count() ? $request->input('is_system_default') : true;

        //if id is returned as null
        $this->emailConfig = !$emailSettings['id'] ? new Emails : Emails::find($emailSettings['id']);

        if(!$this->emailConfig){
            return errorResponse(Lang::get('lang.not_found'));
        }

        //if username passed is empty, it assigns email_address as username
        $emailSettings['user_name'] = $emailSettings['user_name'] ? $emailSettings['user_name'] : $emailSettings['email_address'];

        //creating an object instance
        $this->emailConfig->fill($emailSettings);

        $this->emailConfig->fetching_encryption = $this->emailConfig->fetching_encryption == 'none' ? 'notls' : $this->emailConfig->fetching_encryption;

        $this->emailConfig->sending_encryption = $this->emailConfig->sending_encryption == 'none' ? null : $this->emailConfig->sending_encryption;

        //if fetching status is on but connection is not successful
        if($this->emailConfig->fetching_status && !$this->checkFetchConnection($this->emailConfig)){
            return errorResponse($this->errorhandler());
        }

        //if sending status is on but connection is not successful
        if($this->emailConfig->sending_status && !$this->checkSendConnection($this->emailConfig)){
            return errorResponse($this->errorhandler());
        }

        $this->emailConfig->save();

        if($isSystemDefault){
            $this->updateSystemDefaultMail($this->emailConfig->id);
        }

        return successResponse(Lang::get('lang.successfully_saved'));
    }


    /**
     * makes given mail as default mail
     * @param $id        email database ID
     * return bool
     */
    private function updateSystemDefaultMail($id)
    {
        return Email::first()->update(['sys_email'=>$id]);
    }


    /**
     * takes care of exception handling in this class.
     * NOTE: to make errors user friendly, more and more cases has to be added to it
     * @return string   returns formatted message
     */
    private function errorhandler()
    {
        $message = method_exists($this->error, 'getMessage') ? $this->error->getMessage() : $this->error;

        $helpLink = helpMessage("email_settings_errors");

        return $message. $helpLink;
    }

    /**
     * Display a listing of the Emails.
     * @param Emails $emails
     * @return view
     */
    public function index(Emails $email)
    {
        try {
            // fetch all the emails from emails table
            $emails = $email->get();

            return view('themes.default1.admin.helpdesk.emails.emails.index', compact('emails'));
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * method to get Emails List.
     * @param Request $request
     * @return response
     */
    public function getEmailsList(Request $request)
    {   
        $searchString = $request->input('search-query') ? $request->input('search-query') : '';
        $sortOrder = $request->input('sort-order') ? : 'asc';
        $sortField = $request->input('sort-field') ? : 'updated_at';
        $limit = $request->input('limit') ?: 10;
        $emails = Emails::with(['priority','department','helptopic'])
                  ->select('id','email_address','email_name','created_at','updated_at','sending_status','priority','department', 'help_topic');

        // to fetch emails list having outgoing email settings configured which will be used in Email Diagnostics from list
        if($request->boolean('outgoing_emails'))
        {   
            return successResponse('',$this->getEmailListForEmailDiagnostics($emails, $request));
        }

        $emails = $emails->when($searchString, function ($q) use ($searchString) {
            return $q->where('email_address', 'LIKE', "%$searchString%");
                    })->orderBy($sortField,  $sortOrder)->paginate($limit)->toArray();

        $emails['emails'] = $emails['data'];
        unset($emails['data']);

        foreach ($emails['emails'] as &$email) {
            $email['help_topic'] = $email['helptopic'];
            unset($email['helptopic']);
        }

        return successResponse('',$emails);
    }


    /**
     * method to get email list for email diagnostics
     * @param $emails
     * @param $request
     * @return array
     */
    public function getEmailListForEmailDiagnostics($emails, $request)
    {
        $emails = $emails->where('sending_status',1)->simplePaginate($request->input('limit') ?: 10)->toArray();
        foreach ($emails['data'] as &$email) {
            $email['name'] = $email['email_name'].' ( '.$email['email_address'].' )';
        }

        return $emails;
    }

    /**
     * method to delete email
     * @param Emails $email
     * @return response
     */
    public function delete(Emails $email)
    {
        // fetching the details on the basis of the $id passed to the function
        $defaultSystemEmail = Email::where('id', '=', '1')->first();
        if ($defaultSystemEmail->sys_email) {
            // checking if the default system email is the passed email
            if ($email->id == $defaultSystemEmail->sys_email) {
                return errorResponse(Lang::get('lang.you_cannot_delete_system_default_email'));
            }
        }
        try {
            $email->delete();
            return successResponse(Lang::get('lang.email_deleted_sucessfully'));
        } catch (Exception $e) {
            // returns if the try fails
            return errorResponse($e->getMessage());
        }
    }

    /**
     * handles the view for creating and configuring a new email
     * @return type Response
     */
    public function create()
    {
        return view('themes.default1.admin.helpdesk.emails.emails.create');
    }

    /**
     * handles the view for editing email settings
     * @param integer             $id of the email to be edited
     * @return view
     */
    public function edit($id)
    {
        return view('themes.default1.admin.helpdesk.emails.emails.edit');
    }

}
