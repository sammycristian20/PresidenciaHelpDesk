<?php

namespace App\Http\Controllers\Agent\helpdesk;

// controller
use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\PhpMailController;
// request
use App\Model\helpdesk\Agent\Department;
// model
use App\Model\helpdesk\Agent\Teams;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Manage\Sla_plan;
use App\Model\helpdesk\Ticket\Ticket_Priority;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Workflow\WorkflowAction;
use App\Model\helpdesk\Workflow\WorkflowName;
use App\Model\helpdesk\Workflow\WorkflowRules;
use App\User;
use Datatable;
//classes
use Exception;
use Illuminate\Http\Request;
use Lang;
use Input;
use File;

/**
 * WorkflowController
 * In this controller in the CRUD function for all the workflow applied in faveo.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class HelpsectionController extends Controller
{
    /**
     * Create a new controller instance.
     * constructor to check
     * 1. authentication
     * 2. user roles
     * 3. roles must be agent.
     *
     * @return void
     */
    public function __construct(PhpMailController $PhpMailController)
    {
         $this->PhpMailController = $PhpMailController;
        // checking authentication
        $this->middleware('auth');
        // checking admin roles
        $this->middleware('roles');
    }

    /**
     * Display a listing of all the workflow.
     *
     * @return type
     */
    public function sentmail(Request $request)
    {

        try {

                $name='Helpsection';
                $email=$request->help_email;
                $subject = $request->help_subject;
                $massage = $request->help_massage;
                $helpdocs = $request->helpdocs;
                $scenario=null;

             if (\Input::hasFile('helpdocs')) {
                $attach = \Input::file('helpdocs');
            } else {
                $attach = "";
            }

       
            $files = [];
            if($attach != ""){
                foreach($attach as $attachment){
                            if(is_object($attachment)){
                                $pathToFile = $attachment->getClientOriginalName();
                                $destinationPath = public_path('uploads/helpsection/');
                                 $files[] = $attachment->move($destinationPath, $pathToFile);
                              }
                        }
                   
               }

             $this->PhpMailController->sendEmail($from = $this->PhpMailController->mailfrom('1', '0'), $to = ['email' => $email], $message = ['subject' => $subject, 'body' => $massage,'attachments'=>$files,'scenario'=> $scenario], $template_variables = [null]);
              if($attach != ""){
                $success = File::cleanDirectory($destinationPath);
            }
                return "Message Sent! Thanks for reaching out! Someone from our team will get back to you soon.";
             // return Lang::get('lang.we_receved_your_massage_get_back_to_you');
            // return view('themes.default1.admin.helpdesk.manage.workflow.index');
        } catch (Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

 
}
