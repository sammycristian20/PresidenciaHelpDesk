<?php

namespace App\Plugins\SMS\Controllers;

//controllers
use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\TemplateVariablesController as Template;
use App\Plugins\SMS\Controllers\Msg91Controller;
//models
use App\Plugins\SMS\Model\TemplateSets;
use App\Plugins\SMS\Model\TemplateType;
use App\Model\helpdesk\Settings\System;
//classes
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\support\Collection;
use DB;
use Lang;

class SMSTemplateController extends Controller
{
    public function createSMSTemplates()
    {
        //checking if table already exists and return back if it does
        if (\Schema::hasTable('sms_template_sets')) {
            return true;
        }
        // otherwise create template tables
        \Schema::create('sms_template_sets', function (Blueprint $table) {
            $table->increments('id'); //stores id of a new entry
            $table->string('name', 100);
            $table->integer('status')->default(0);
            $table->integer('is_default')->default(0);
            $table->string('template_language', 10)->default('en');
            $table->timestamps();//stores time_stamp "created_at" , "updated_at"
        });
        \Schema::create('sms_template_types', function (Blueprint $table) {
            $table->increments('id'); //stores id of a new entry
            $table->string('type', 100);
            $table->text('description');
            $table->text('body');
            $table->integer('event_type');
            $table->string('template_category', 100)->nullable();
            $table->integer('set_id')->unsigned();
            $table->timestamps();//stores time_stamp "created_at" , "updated_at"
        });
        \Schema::table('sms_template_types', function ($table) {
            $table->foreign('set_id')->references('id')->on('sms_template_sets')->onDelete('RESTRICT');
        });
        $template_set = new \App\Plugins\SMS\Model\TemplateSets;
        if ($template_set->count() == 0) {
            $template_set->create([
                'name'       => 'System default',
                'status'     => 1,
                'is_default' => 1,
            ]);
        }
        $this->createTemplateTypes(0);
    }

    /**
     * @category function to get SMS template sets table view
     * @param null
     * @return response/view
     */
    public function showSMSTemplates()
    {
        $path = base_path().DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'lang';
        $langs = scandir($path);
        $langs = array_diff($langs, ['.', '..']);
        $languages = [];
        foreach ($langs as $lang) {
            $languages[$lang] = (Lang::getLocale() != 'ar') ? \Config::get('languages.'.$lang)[0].'&nbsp;('.\Config::get('languages.'.$lang)[1].')' : \Config::get('languages.'.$lang)[0].'&nbsp;&rlm;('.\Config::get('languages.'.$lang)[1].')' ;
        }
        return view('SMS::template.show', compact('languages'));
    }

    /**
     * @category function to show SMS template set list
     * @param null
     * @return json response for datatable
     */
    public function getTemplates()
    {
        $template_set = TemplateSets::get();
        return \DataTables::collection(new Collection($template_set))
            ->addColumn('id2', function ($set) {
                return $set->name;
            })
            ->addColumn('id3', function ($set) {
                if ($set->status == 1) {
                    return "<span style='color: green'>".Lang::get('SMS::lang.active')."</span>";
                } else {
                    return "<span style='color: red'>".Lang::get('SMS::lang.inactive')."</span>";
                }
            })
            ->addColumn('id4', function ($set) {
                $lang = 'languages.'.$set->template_language;
                if (Lang::getLocale() == 'ar') {
                    return \Config::get($lang)[0].'&nbsp;&rlm;('.\Config::get($lang)[1].')';
                }
                return \Config::get($lang)[0].'&nbsp;('.\Config::get($lang)[1].')';
            })
            ->addColumn('id5', function ($set) {
                $return = '';
                if ($set->status == 1 && $set->id == 1) {
                    $return = "<a  href='".route('activate-set', $set->id)."' href='#' class='btn btn-primary btn-xs disabled'><i class='fa fa-check-circle'>  </i> ".Lang::get('SMS::lang.activate_this_set')."</a>";
                } elseif ($set->status == 1 && $set->id != 1) {
                    $return = "<a href='".route('activate-set', $set->id)."' class='btn btn-primary btn-xs'><i class='fa fa-check-circle'>  </i> ".Lang::get('lang.deactivate_this_set')."</a>";
                } else {
                    $return = "<a  href='".route('activate-set', $set->id)."' href='#' class='btn btn-primary btn-xs'><i class='fa fa-check-circle'>  </i> ".Lang::get('SMS::lang.activate_this_set')."</a>";
                }
                $return .= "&nbsp;<a href='".route('show-set', $set->id)."' class='btn btn-xs btn-primary'><i class='fa fa-eye'>  </i> ".Lang::get('lang.view')."</a>&nbsp;";
                $return .= '<button class="btn btn-primary btn-xs" id="delete'.$set->id.'" onclick="return confirmDelete('.$set->id.')"><i class="fa fa-trash" style="color:white;">&nbsp; </i> '.Lang::get('lang.delete').'</button>';
                return $return;
            })
            ->rawColumns(['id3', 'id4', 'id5'])
            ->make();
    }

    /**
     * @category function for creating new template set
     * @param Request object
     * @return response with session variables
     */
    public function createTemplateSet(Request $req)
    {
        if ($req->input('folder_name') == '' || $req->input('folder_name') == null) {
            return back()->with('fails', 'Template set\'s name is required');
        } else {
            $template = TemplateSets::where('template_language', '=', $req->input('template_language'))->count();
            $status = 1;
            if ($template > 0) {
                $status = 0;
            }
            $var = new TemplateSets;
            $var->name = $req->input('folder_name');
            $var->status = $status;
            $var->template_language = $req->input('template_language');
            $var->save();
            $this->createTemplateTypes($var->id);
            return back()->with('success', Lang::get('SMS::lang.sms-template-created-successfully'));
        }
    }

    /**
     * @category function to add template types
     * @param integer $id (id of teplate set)
     * @return boolean true/false
     */
    public function createTemplateTypes($id)
    {
        if ($id == 0) {
            $id = 1;
        }

        $template_type = new TemplateType;
        //common
        //common templates
        $template_type->create([
            'template_category' => 'common-tmeplates',
            'description' => 'template-register-confirmation-with-account-details',
            'type' => 'registration-notification',
            'event_type' => '7',
            'set_id' => $id,
            'body' => '<p>Hello {!! $receiver_name !!},<br>'
                    .'Your registration has been confirmed. Below are your login details.<br>'
                    .'Registered Email: {!! $new_user_email !!}<br>'
                    .'Password: {!! $user_password !!}</p>'
                    .'<p>Thanks</p>'
        ]);

        $template_type->create([
            'template_category' => 'common-tmeplates',
            'description' => 'template-sms-otp-verification-to-users',
            'type' => 'otp-verification',
            'event_type' => '22',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'Your OTP verification code is {!! $otp_code !!}'
                .'Thanks'
        ]);

        $template_type->create([
            'template_category' => 'common-tmeplates',
            'description' => 'template-reset-password-link',
            'type' => 'reset-password',
            'event_type' => '8',
            'set_id' => $id,
            'body' => '<p>Hello {!! $receiver_name !!},<br><br>'
                .'Please follow the link to reset your password</p>'
                .'<p>{!! $password_reset_link !!}<br></p>'
                .'<p>Thanks</p>'
        ]);

        $template_type->create([
            'template_category' => 'common-tmeplates',
            'description' => 'template-new-password',
            'type' => 'reset_new_password',
            'event_type' => '15',
            'set_id' => $id,
            'body' => '<p>Hello {!! $receiver_name !!},<br><br>'
                .'Your password is successfully changed.Your new password is : {!! $user_password !!}<br><br>'
                .'Thank You.</p>'
        ]);

        $template_type->create([
            'template_category' => 'common-tmeplates',
            'description' => 'template-register-confirmation-with-activation-link',
            'type' => 'registration',
            'event_type' => '11',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br><br>'
                .'Please click on the below link to activate your account and Login to the system<br>'
                .'{!! $account_activation_link !!}<br><br>'
                .'Thank You.'
        ]);






        // client tepmlates
        $template_type->create([
            'description' => 'template-ticket-checking-wihtout-login-link',
            'type' => 'check-ticket',
            'event_type' => '2',
            'template_category' => 'client-templates',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br><br>'
                .'Click the link below to view your requested ticket<br>'
                .'{!! $ticket_link !!}<br>'
                .'<p>Thanks</p>'
        ]);

        $template_type->create([
            'description' => 'template-ticket-creation-acknowledgement-client-by-client',
            'type' => 'create-ticket',
            'event_type' => '4',
            'template_category' => 'client-templates',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!}<br>'
                .'Thank you for contacting us. Your request has been registered in our system.<br>'
                .'Ticket ID: {!! $ticket_number !!}<br>'
                .'To check the status of your ticket follow the link below<br>'
                .'{!! $ticket_link !!}<br>'
                .'Thanks'
        ]);

        $template_type->create([
            'template_category' => 'client-templates',
            'description' => 'template-ticket-status-update-client',
            'type' => 'status-update',
            'event_type' => '21',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'This message is regarding your ticket {!! $ticket_number !!}<br/>'
                .'{!! $message_content !!}<br>'
                .'If you are not satisfied please respond to the ticket here {!! $ticket_link !!}<br>'
                .'Thank You'
        ]);

        // $template_type->create([
        //     'id' => '6',
        //     'variable' => '0',
        //     'description' => 'template-ticket-creation-acknowledgement-client-by-agent',
        //     'type' => 'create-ticket-by-agent', 'event_type' => '6',
        //     'template_category' => 'client-templates',
        //     'set_id' => $id
        // ]);

        $template_type->create([
            'description' => 'template-ticket-assignment-notice-to-client',
            'type' => 'assign-ticket',
            'event_type' => '1',
            'template_category' => 'client-templates',
            'set_id' => $id,
            'body' => '<p>Hello {!! $receiver_name !!},<br>'
                .'Your ticket with ID: {!! $ticket_number !!} has been assigned to one of our agents. </p>'
                .'<p>They will contact you soon.</p>'
                .'<p>Thanks</p>'
        ]);

        $template_type->create([
            'template_category' => 'client-templates',
            'description' => 'template-ticket-reply-to-client-by-agent',
            'type' => 'ticket-reply',
            'event_type' => '9',
            'set_id' => $id,
            'body' => '<p>Hello {!! $receiver_name !!}</p>'
                .'<p>Our agent has replied on your ticket with id {!! $ticket_number !!}</p>'
                .'<p>Follow the link below to check your ticket.</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'<p>Thanks</p>'
        ]);

        $template_type->create([
            'template_category' => 'client-templates',
            'description' => 'template-ticket-assigment-notice-to-team-client',
            'type' => 'team_assign_ticket',
            'event_type' => '14',
            'set_id' => $id,
            'body' => '<p>Hello {!! $receiver_name !!},<br>'
                .'Your ticket with ID: {!! $ticket_number !!} has been assigned to our {!! $assigned_team !!} team. </p>'
                .'<p>They will contact you soon.</p>'
                .'<p>Thanks</p>'
        ]);

        
        // Assigend agent templates
        $template_type->create([
            'description' => 'template-ticket-assignment-notice-to-assigned-agent',
            'type' => 'assign-ticket',
            'event_type' => '1',
            'template_category' => 'assigend-agent-templates',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br><br>'
                .'Ticket No: {!! $ticket_number !!}<br>'
                .'Has been assigned to you by {!! $activity_by !!}<br>'
                .'<p>Thanks</p>'
        ]);

        $template_type->create([
            'template_category' => 'assigend-agent-templates',
            'description' => 'template-ticket-reply-to-assigned-agents-by-client',
            'type' => 'ticket-reply-agent',
            'event_type' => '10',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'Client has made a new reply on their ticket which is assigned to you.<br>'
                .'Ticket ID: {!! $ticket_number !!}'
                .'<p>Follow the link to reply</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'<p>Thanks</p>'
        ]);

        $template_type->create([
            'template_category' => 'assigend-agent-templates',
            'description' => 'template-response-voilate-escalation-to-assigned-agent',
            'type' => 'response_due_violate',
            'event_type' => '12',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'<p>Response is due on your assigned ticket {!! $ticket_number !!}</p>'
                .'<p>Follow the link to respond</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'<p>Thanks</p>'
        ]);
        $template_type->create([
            'template_category' => 'assigend-agent-templates',
            'description' => 'template-resolve-voilate-escalation-to-assigned-agent',
            'type' => 'resolve_due_violate',
            'event_type' => '13',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'<p>Resolution is due on your assigned ticket {!! $ticket_number !!}</p>'
                .'<p>Follow the link to respond</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'<p>Thanks</p>'
        ]);

        $template_type->create([
            'template_category' => 'assigend-agent-templates',
            'description' => 'template-internal-change-to-assigned-agent',
            'type' => 'internal_change',
            'event_type' => '16',
            'set_id' => $id,
            'body' => '<p>Hello {!! $receiver_name !!},<br><br>'
                .'This message is regarding your ticket with ticket ID {!! $ticket_number !!}.<br>'
                .'{!! $message_content !!}.<br>'
                .'By {!! $activity_by !!}<br>'
                .'Thanks'
        ]);
        
        $template_type->create([
            'template_category' => 'assigend-agent-templates',
            'description' => 'template-response-time-approach-to-assigned-agents',
            'type' => 'response_due_approach',
            'event_type' => '17',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'<p>Response duration of SLA is approaching on your assigned ticket {!! $ticket_number !!}</p>'
                .'<p>Follow the link to respond</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'<p>Thanks</p>'

        ]);
        
        $template_type->create([
            'template_category' => 'assigend-agent-templates',
            'description' => 'template-resolve-time-approach-to-assigned-agents',
            'type' => 'resolve_due_approach',
            'event_type' => '18',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'<p>Resolution durations of SLA is approaching on your assigned ticket {!! $ticket_number !!}</p>'
                .'<p>Follow the link to respond</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'<p>Thanks</p>'
        ]);

        
        $template_type->create([
            'template_category' => 'assigend-agent-templates',
            'description' => 'template-ticket-status-update-assign-agent',
            'type' => 'status-update',
            'event_type' => '21',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'This message is regarding your ticket {!! $ticket_number !!}<br/>'
                .'{!! $message_content !!}<br>'
                .'Thank You'
        ]);

        $template_type->create([
            'template_category' => 'assigend-agent-templates',
            'description' => 'template-ticket-reply-to-assigned-agents-by-agent',
            'type' => 'ticket-reply',
            'event_type' => '9',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br><br>'
                .'A reply has been made to ticket assigned to you with ID: {!! $ticket_number !!} by {!! $activity_by !!} in our helpdesk.<br>'
                .'<p>Please follow the link below to check</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'Thanks'
        ]);


        //Other agent templates
        
        $template_type->create([
            'description' => 'template-new-ticket-creation-notice-agents',
            'type' => 'create-ticket-agent',
            'event_type' => '5',
            'template_category' => 'agent-templates',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'New ticket with ID: {!! $ticket_number !!} has been created in our helpdesk.<br>'
                .'Ticket ID: {!! $ticket_number !!}<br>'
                .'<p>Follow the link to check ticket</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'Thanks'
        ]);
        
        
        $template_type->create([
            'description' => 'template-ticket-reply-to-agents-by-client',
            'template_category' => 'agent-templates',
            'type' => 'ticket-reply-agent',
            'event_type' => '10',
            'set_id' => $id,
            'body' => '<p>Hello {!! $receiver_name !!},<br>'
                .'Client has made a new reply on their ticket in our helpdesk system.<br>
                '.'Ticket ID: {!! $ticket_number !!}<br>'
                .'Please follow the link below to check and reply on ticket.<br>'
                .'{!! $ticket_link !!}<br><br>'
                .'Thanks'
        ]);
        
        $template_type->create([
            'template_category' => 'agent-templates',
            'description' => 'template-response-voilate-escalation-to-agent',
            'type' => 'response_due_violate',
            'event_type' => '12',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'<p>Response is due on ticket {!! $ticket_number !!}</p>'
                .'<p>Follow the link to respond</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'<p>Thanks</p>'
        ]);
        $template_type->create([
            'template_category' => 'agent-templates',
            'description' => 'template-resolve-voilate-escalation-to-agent',
            'type' => 'resolve_due_violate',
            'event_type' => '13',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'<p>Resolution is due on ticket {!! $ticket_number !!}</p>'
                .'<p>Follow the link to respond</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'<p>Thanks</p>'
        ]);
        $template_type->create([
            'template_category' => 'agent-templates',
            'description' => 'template-ticket-assigment-notice-to-team',
            'type' => 'team_assign_ticket',
            'event_type' => '14',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'Ticket No: {!! $ticket_number !!}<br>'
                .'Has been assigned to your team {!! $assigned_team_name !!} by {!! $activity_by !!}<br>'
                .'Follow the link below to check and reply on the ticket.<br>'
                .'{!! $ticket_link !!}<br><br>'
                .'Thank You'
        ]);
        
        
        $template_type->create([
            'template_category' => 'agent-templates',
            'description' => 'template-internal-change-to-agent',
            'type' => 'internal_change',
            'event_type' => '16',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br><br>'
                .'This message is regarding ticket with ticket ID {!! $ticket_number !!}.<br>'
                .'{!! $message_content !!}.<br>'
                .'By {!! $activity_by !!}<br><br>'
                .'Thank you'
        ]);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
        $template_type->create([
            'template_category' => 'agent-templates',
            'description' => 'template-response-time-approach-to-agents',
            'type' => 'response_due_approach',
            'event_type' => '17',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'<p>Response duration of SLA is approaching on ticket {!! $ticket_number !!}</p>'
                .'<p>Follow the link to respond</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'<p>Thanks</p>'
        ]);
        
        $template_type->create([
            'template_category' => 'agent-templates',
            'description' => 'template-resolve-time-approach-to-agents',
            'type' => 'resolve_due_approach',
            'event_type' => '18',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'<p>Resolution durations of SLA is approaching on ticket {!! $ticket_number !!}</p>'
                .'<p>Follow the link to respond</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'<p>Thanks</p>'
        ]);
        
        $template_type->create([
            'template_category' => 'agent-templates',
            'description' => 'template-new-user-entry-notice',
            'type' => 'new-user',
            'event_type' => '19',
            'set_id' => $id,
            'body' => '<p>Hello {!! $receiver_name !!},<br>'
                .'A new user has been registered in our helpdesk system.<br>User Details<br>'
                .'Name: {!! $new_user_name !!}<br>'
                .'Email: {!! $new_user_email !!}</p>'
                .'Thanks'
        ]);
        
        $template_type->create([
            'template_category' => 'agent-templates',
            'description' => 'template-non-assign-escalation-notice',
            'type' => 'no_assign_message',
            'event_type' => '20',
            'set_id' => $id
        ]);
        
        $template_type->create([
            'template_category' => 'agent-templates',
            'description' => 'template-ticket-status-update-agent',
            'type' => 'status-update',
            'event_type' => '21',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'This message is regarding {!! $ticket_number !!}<br/>'
                .'{!! $message_content !!}<br>'
                .'Thank You'
        ]);

        $template_type->create([
            'description' => 'template-ticket-assignment-notice-to-agent',
            'type' => 'assign-ticket',
            'event_type' => '1',
            'template_category' => 'agent-templates',
            'set_id' => $id,
            'body' => '<p>Hello {!! $receiver_name !!},<br>'
                .'Ticket No: {!! $ticket_number !!}<br>'
                .'Has been assigned to {!! $agent_name !!} by {!! $activity_by !!}<br>'
                .'Thank You'
        ]);

        $template_type->create([
            'template_category' => 'agent-templates',
            'description' => 'template-ticket-reply-to-agents-by-agent',
            'type' => 'ticket-reply',
            'event_type' => '9',
            'set_id' => $id,
            'body' => 'Hello {!! $receiver_name !!},<br>'
                .'An agent has replied to ticket with ID: {!! $ticket_number !!} in our helpdesk system.'
                .'<p>Follow the link to check ticket</p>'
                .'<p>{!! $ticket_link !!}</p>'
                .'Thanks'
        ]);
        return true;
    }

    /**
     * @category function to delete template sets
     * @param Request object
     * @return response with message
     */
    public function deleteTemplateSet(Request $req)
    {
        $defactive = DB::table('sms_template_sets')->select('id')
            ->where('status', '=', 1)->orWhere('is_default', '=', 1)
            ->get();
        $omit = [];
        foreach ($defactive as $value) {
            array_push($omit, $value->id);
        }
        $var = DB::table('sms_template_sets')
                    ->whereIn('id', $req->input('select_all'))
                    ->where([['status', '<>', 1], ['is_default', '<>', 1]])
                    ->delete();
        $var2 = DB::table('sms_template_types')
                    ->whereIn('set_id', $req->input('select_all'))
                    ->whereNotIn('set_id', $omit)
                    ->delete();
        if ($var > 0 && ($var2%33) == 0) {
            return back()->with('success', Lang::get('SMS::lang.template-set-deleted-successfully'));
        } else {
            return back()->with('fails', Lang::get('SMS::lang.template-set-deletion-error'));
        }
    }

    /**
     * @category function to activate a template set
     * @param integer $id (id of template set)
     * @return resposne with message
     */
    public function activateTemplates($id)
    {
        $status = '';
        $message = '';
        $currnet_status = TemplateSets::select('status', 'template_language')->where('id', '=', $id)->first();
        if (!$currnet_status->status) {
            TemplateSets::where('template_language', '=', $currnet_status->template_language)
                ->update(['status' => 0]);
            $change = TemplateSets::where('id', '=', $id)->update([
                'status' => 1
            ]);
            if ($change) {
                $status = 'success';
                $message = Lang::get('SMS::lang.template-set-activated-successfully');
                // return back()->with('success', Lang::get('SMS::lang.template-set-activated-successfully'));
            } else {
                $status = 'fails';
                $message = 'Could not activate the set please try later';
                // return back()->with('fails', '');
            }
        } else {
            $status = 'success';
            $message = Lang::get('lang.you_have_successfully_deactivated_this_set');
            $change = TemplateSets::where('id', '=', $id)->update([
                'status' => 0
            ]);
            if (!$this->checkDefaultIsInactive($id)) {
                TemplateSets::where('id', '=', 1)->update([
                    'status' => 1
                ]);
                $message = Lang::get('lang.you_have_successfully_deactivated_this_set_made_system_default_active');
            }
        }
        return redirect()->back()->with($status, $message);
    }

    /**
     * @category function to show template type tables
     * @param integer $id (id of template set)
     * @return resposne/view
     */
    public function showTemplatesType($id)
    {
        $template = TemplateSets::select('id')->where('id', '=', $id)->first();
        if (!$template) {
            return redirect()->route('sms-template-sets')->with('fails', Lang::get('SMS::lang.template-set-not-found'));
        } else {
            
            return view('SMS::template.showtype', compact('id'));
        }
    }

    /**
     * @category function to get templatetype list
     * @param integer $id (id of templatem set)
     * @return json response for datatable
     */
    public function getTemplatesType($id)
    {
        $template_types = TemplateType::select('id', 'type', 'description', 'template_category')->where('set_id', '=', $id)->get();
        return \DataTables::collection(new Collection($template_types))
        ->addColumn('type', function ($type) {
            return $type->type;
        })
        ->addColumn('description', function ($type) {
            return Lang::get('lang.'.$type->description);
        })
        ->addColumn('action', function ($type) {
            return '<a href="'.route('edit-template', $type->id).'" class="btn btn-primary btn-xs"><i class="fa fa-edit" style="color:white;">  </i>'.Lang::get('SMS::lang.edit').'</a>';
        })
        ->addColumn('template_category', function($type){
            return Lang::get('lang.'.$type->template_category);
        })
        ->make();
    }

    /**
     * @category function for editing template
     * @param integer $id (id of template)
     * @return response view
     */
    public function editTemplatesType($id)
    {
        $template = TemplateType::select('id', 'type', 'body', 'description', 'event_type')
        ->where('id', '=', $id)->first();
        if (!$template) {
            return redirect()->route('sms-template-sets')->with('fails', Lang::get('SMS::lang.template-not-found'));
        } else {
            $type = $template->type;
            $description = $template->description;
            $event_type = $template->event_type;
            $body = $template->body;
            $template = new Template();
            $body = $template->stringReplaceVariables($body);
            $var = $template->getAvailableTemplateVariables($type);
            return view('SMS::template.edittemplate', compact('id', 'type', 'description', 'body', 'var', 'event_type'));
        }
    }

    /**
     * @category function to update template
     * @param Request object
     * @return response
     */
    public function postTemplateEdit(Request $req)
    {
        if ($req->input('message') == null || $req->input('message') == '') {
            return redirect()->back()->with('fails', 'Content is required.');
        } else {
            $body = $req->input('message');
            $id = $req->input('id');
            $event_type = $req->input('event_type');
            $template = new Template();
            $body = $template->stringReplaceVariablesReverse($body);
            TemplateType::where('id', '=', $id)
                ->update(['body' => $body]);
            return redirect()->back()->with('success', Lang::get('SMS::lang.template-updated-successfully'));
        }
    }

    /**
     *@category function to get template body on basis of template type
     *@param string $type
     *@return string $content
     */
    public function getTemplateBody($template_type, $role, $assigned_agent = false, $lang = null)
    {
        if ($template_type == 'registration-notification' || $template_type == 'reset-password' || $template_type == 'reset_new_password' || $template_type == 'registration' || $template_type == 'otp-verification') {
            $template_category = 'common-tmeplates';
        } elseif ($assigned_agent) {
            $template_category = 'assigend-agent-templates';
        } elseif ($role == 'user' || $template_type == 'check-ticket' || $template_type == 'check-ticket' || $template_type == 'create-ticket') {
            $template_category = 'client-templates';
        } else {
            $template_category = 'agent-templates';
        }

        if ($lang == null) {
            $set = TemplateSets::select('id')->where('status', '=', 1)->first();
        } else {
            $set = TemplateSets::select('id')->where('template_language', '=', $lang)->first();
        }
        if ($set == null) {
            $set = TemplateSets::select('id')->where('id', '=', 1)->first();
        }
        $template = TemplateType::select('body')->where('set_id', '=', $set->id)->where('type', '=', $template_type)->where('template_category', '=', $template_category)->first();
        $content = $template->body;
        return $content;
    }

     /**
     * @category function to check whether system has active template set other than the one with id passed in arguments
     * @param int $id
     * @var boolean $active
     * @return boolean $active
     */
    public function checkDefaultIsInactive($id)
    {
        $active = true;
        $default_template = TemplateSets::where('status', '=', 1)->select('id')->get()->toArray();
        if (count($default_template) == 0) {
            $active = false;
        }
        return $active;
    }
}
