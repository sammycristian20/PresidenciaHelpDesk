<?php

namespace App\Http\Controllers\Client\helpdesk;

// controllers
use App\Http\Controllers\Common\PhpMailController;
use App\Http\Controllers\Controller;
// requests
use App\Model\helpdesk\Email\Emails;
// models
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Ticket\TicketToken;
use App\User;
use DB;
// classes
use Hash;
use Illuminate\Http\Request;
use Input;
use Lang;
use Auth;
use Session;
use Crypt;
use Carbon\Carbon;
use App\Http\Controllers\Agent\helpdesk\TicketController;

/**
 * GuestController.
 *
 * @author      Ladybird <info@ladybirdweb.com>
 */
class UnAuthController extends Controller
{
    /**
     *@var TicketController
     *
     */
    protected $ticketController;

    /**
     * Create a new controller instance.
     *
     * @return type void
     */
    public function __construct(PhpMailController $PhpMailController)
    {
        $this->middleware('board', ['except' => ['changeUserLanguage','boardOfflineView','errorInDatabaseConnectionView', 'unauthorizedView']]);
        $this->PhpMailController = $PhpMailController;
        $this->ticketController = new TicketController;
    }

    /**
     * Post Check ticket.
     *
     * @param type CheckTicket   $request
     * @param type User          $user
     * @param type Tickets       $ticket
     * @param type Ticket_Thread $thread
     *
     * @return type Response
     */
    public function PostCheckTicket(Request $request)
    {

        try {

            $validator = \Validator::make($request->all(), [
                        'email_address' => 'required|email',
                        'ticket_number' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                                ->withErrors($validator)
                                ->withInput()
                                ->with('check', '1');
            }
            $email = $request->input('email_address');
            $ticket_number = $request->input('ticket_number');
            // get user details
            $user_details = User::where('email', '=', $email)->first();
            if ($user_details == null) {
                
                return errorResponse(Lang::get('lang.sorry_that_email_is not_available_in_this_system'));

            }
            // get ticket details
            $ticket = Tickets::where('ticket_number', '=', $ticket_number)->first();
            if ($ticket == null) {

                return errorResponse(Lang::get('lang.there_is_no_such_ticket_number'));

            }
            if ($ticket->user_id == $user_details->id) {
                if ($user_details->role == 'user') {
                    $username = $user_details->first_name;
                } else {
                    $username = $user_details->first_name.' '.$user_details->last_name;
                }
                // check for preentered ticket token
                $ticket_token = TicketToken::where('ticket_id', '=', $ticket->id)->first();
                if ($ticket_token) {
                    $token = $this->generate_random_ticket_token();
                    $hashed_token = Crypt::encryptString($ticket->id);
                    $ticket_token->token = $hashed_token;
                    $ticket_token->save();
                } else {
                    $ticket_token = new TicketToken();
                    $ticket_token->ticket_id = $ticket->id;
                    $token = $this->generate_random_ticket_token();
                    $hashed_token = Crypt::encryptString($ticket->id);
                    $ticket_token->token = $hashed_token;
                    $ticket_token->save();
                }
                try {
                    $this->PhpMailController->sendmail(
                            $from = $this->PhpMailController->mailfrom('1', '0'), $to = ['name' => $username, 'email' => $user_details->email], $message = ['subject' => 'Ticket link Request ['.$ticket_number.']', 'scenario' => 'check-ticket'], $template_variables = ['user' => $username, 'ticket_link' => url('show-ticket/'.$hashed_token), 'ticket_subject' => title($ticket->id),
                            'ticket_number' => $ticket->ticket_number]
                    );
                } catch (\Exception $e) {
                }

                // return redirect()->back()
                //                 ->with('success', Lang::get('lang.we_have_sent_you_a_link_by_email_please_click_on_that_link_to_view_ticket'));

            return successResponse(
                Lang::get('lang.we_have_sent_you_a_link_by_email_please_click_on_that_link_to_view_ticket'));

            } else {
                return errorResponse(Lang::get("lang.email_didn't_match_with_ticket_number"));

                // return \Redirect::route('form')->with('fails', Lang::get("lang.email_didn't_match_with_ticket_number"));
            }
        } catch (\Exception $e) {

            return errorResponse($e->getMessage());

            // return \Redirect::route('form')->with('fails', $e->getMessage());
        }
    }

    /**
     * generate random string token for ticket.
     *
     * @param type $length
     *
     * @return string
     */
    public function generate_random_ticket_token($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Store ratings of the user.
     *
     * @return type Redirect
     */
    public function rating($id, Request $request, \App\Model\helpdesk\Ratings\RatingRef $rating_ref)
    {
        foreach ($request->all() as $key => $value) {
            if (strpos($key, '_') !== false) {
                $ratName = str_replace('_', ' ', $key);
            } else {
                $ratName = $key;
            }
            $ratID = \App\Model\helpdesk\Ratings\Rating::where('name', '=', $ratName)->first();
            $ratingrefs = $rating_ref->where('rating_id', '=', $ratID->id)->where('ticket_id', '=', $id)->first();
            if ($ratingrefs !== null) {
                $ratingrefs->rating_id = $ratID->id;
                $ratingrefs->ticket_id = $id;

                $ratingrefs->thread_id = '0';
                $ratingrefs->rating_value = $value;
                $ratingrefs->save();
            } else {
                $rating_ref->rating_id = $ratID->id;
                $rating_ref->ticket_id = $id;

                $rating_ref->thread_id = '0';
                $rating_ref->rating_value = $value;
                $rating_ref->save();
            }
        }

        return redirect()->back()->with('Success', Lang::get('lang.thank_you_for_your_rating'));
    }

    /**
     * Store Client rating about reply of agent quality.
     *
     * @return type Redirect
     */
    public function ratingReply($id, Request $request, \App\Model\helpdesk\Ratings\RatingRef $rating_ref)
    {
        foreach ($request->all() as $key => $value) {
            $key1 = explode(',', $key);
            if (strpos($key1[0], '_') !== false) {
                $ratName = str_replace('_', ' ', $key1[0]);
            } else {
                $ratName = $key1[0];
            }
            $ratID = \App\Model\helpdesk\Ratings\Rating::where('name', '=', $ratName)->first();
            $ratingrefs = $rating_ref->where('rating_id', '=', $ratID->id)->where('thread_id', '=', $key1[1])->first();
            if ($ratingrefs !== null) {
                $ratingrefs->rating_id = $ratID->id;
                $ratingrefs->ticket_id = $id;
                $ratingrefs->thread_id = $key1[1];
                $ratingrefs->rating_value = $value;
                $ratingrefs->save();
            } else {
                $rating_ref->rating_id = $ratID->id;
                $rating_ref->ticket_id = $id;
                $rating_ref->thread_id = $key1[1];
                $rating_ref->rating_value = $value;
                $rating_ref->save();
            }
        }

        return redirect()->back()->with('Success', Lang::get('lang.thank_you_for_your_rating'));
    }

    /**
     * function to change the status of the ticket.
     *
     * @param type $status
     * @param type $id
     *
     * @return string
     */
    public function changeStatus($status, $id)
    {
        $tickets = Tickets::where('id', '=', $id)->first();
        $tickets->status = $status;
        $ticket_status = Ticket_Status::where('id', '=', $status)->first();
        if ($ticket_status->state == 'closed') {
            $tickets->closed = $ticket_status->id;
            $tickets->closed_at = date('Y-m-d H:i:s');
        }
        $tickets->save();
        $ticket_thread = Ticket_Thread::where('ticket_id', '=', $ticket_status->id)->first();
        $ticket_subject = $ticket_thread->title;

        $user = User::where('id', '=', $tickets->user_id)->first();

        $thread = new Ticket_Thread();
        $thread->ticket_id = $tickets->id;
        $thread->user_id = $tickets->user_id;
        $thread->is_internal = 1;
        $thread->body = $ticket_status->message.' '.$user->user_name;
        $thread->save();

        $email = $user->email;
        $user_name = $user->user_name;

        $ticket_number = $tickets->ticket_number;

        $sending_emails = Emails::where('department', '=', $ticket_status->dept_id)->first();
        if ($sending_emails == null) {
            $from_email = $this->system_mail();
        } else {
            $from_email = $sending_emails->id;
        }
        try {
            $this->PhpMailController->sendmail($from = $this->PhpMailController->mailfrom('0', $tickets->dept_id), $to = ['name' => $user_name, 'email' => $email], $message = ['subject' => $ticket_subject.'[#'.$ticket_number.']', 'scenario' => 'close-ticket'], $template_variables = ['ticket_number' => $ticket_number]);
        } catch (\Exception $e) {
            return 0;
        }

        return Lang::get('lang.your_ticket_has_been').' '.$ticket_status->state;
    }

    /**
     * Function to filter tickets for auto close
     * @param  WorkflowClose  $workflowClose
     * @param  String         $time           Last updated time to compare replies on tickets
     * @return void
     *
     * @todo   To use changeStatus method of TicketController to handle other dependent udpates
     * while changing status such as due_date, resolution etc.
     * @link https://github.com/ladybirdweb/faveo-helpdesk-advance/issues/4666 
     */
    private function getTicketsToAutoClose($workflowClose):void
    {
        $ticketController = new \App\Http\Controllers\Agent\helpdesk\TicketController();
        $tickets = $workflowClose->tickets()->get();
        $tickets = $tickets->each(function ($value, $key) use ($workflowClose, $ticketController) {
            $last = $value->lastThread()->first()->toArray();
            if($last['poster'] == 'client' || $last['user_id'] == $value->user_id) {
                return true; //will not close ticket if last reply is from client or the requester of the ticket(admin/agent) but continue the loop
            }
            //difference of current time and last reply time by agent in days
            $days = Carbon::now()->timezone('UTC')->diff($value->lastThread()->first()->created_at)->days;
            if($days >= $workflowClose->days && $value->status != $workflowClose->closeStatus->id) {
                $value->status = $workflowClose->closeStatus->id;
                $value->save();
                if ($workflowClose->send_email) {
                    $ticketController->sendNotification($value, $workflowClose->closeStatus);
                }
            }
        });
    }

    //Auto-close tickets
    public function autoCloseTickets()
    {
        $workflow = \App\Model\helpdesk\Workflow\WorkflowClose::whereId(1)->first();
        if($workflow->condition == 1) {
            $this->getTicketsToAutoClose($workflow);

            return (int)true;
        }

        return (int)false;
    }

    /**
     *@category function to change system's language
     *
     *@param string $lang //desired language's iso code
     *
     *@return response
     */
    public static function changeLanguage($lang)
    {
        $path = base_path('resources/lang');  // Path to check available language packages
        if (array_key_exists($lang, \Config::get('languages')) && in_array($lang, scandir($path))) {
            \Cache::forever('language', $lang);
            DB::table('settings_system')->where('id', '=', 1)
                ->update(['content' => $lang]);

            return true;
        }

        return false;
    }

    /**
     *@category function to change system's language
     *
     *@param string $lang //desired language's iso getTicketsToAutoClosecode
     *
     *@return response
     */
    public static function changeUserLanguage($lang)
    {
        $path = base_path('resources/lang');  // Path to check available language packages
        if (array_key_exists($lang, \Config::get('languages')) && in_array($lang, scandir($path))) {
            if (\Auth::check()) {
                $id = \Auth::user()->id;
                $user = User::find($id);
                $user->user_language = $lang;
                $user->save();
            } else {
                Session::put('language', $lang);
            }
        }

        return redirect()->back();
    }

    public function callAPI($method, $url, $data = false)
    {
        $curl = curl_init();
        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data) {
                    if(strpos($url, '?')) {
                        $url = sprintf("%s&%s", $url, http_build_query($data));
                    } else {
                        $url = sprintf("%s?%s", $url, http_build_query($data));
                    }
                }
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = [curl_exec($curl), curl_getinfo($curl, CURLINFO_HTTP_CODE)];
        curl_close($curl);

        return $result;
    }

    public function validateUserSession($token_validate_url, $api_variable_name, $api_variable_value, $redirect_url)
    {
        if ($token_validate_url != '' || $token_validate_url != null) {
            $token_validate = json_decode($this->callAPI('GET', $token_validate_url, [$api_variable_name => $api_variable_value]));
            if ($token_validate) {
                if ($token_validate->status == 200) {
                    $user = User::where('user_name', '=', $token_validate->user->user_name);
                    if ($token_validate->user->email != null) {
                        $user = $user->orWhere('email', '=', $token_validate->user->email);
                    }
                    $user = $user->select('id')->first();
                    if(count($user) ==1) {
                        Auth::loginUsingId($user->id);
                        return true;
                    } else {
                        return $redirect_url."?error=user_not_found";
                    }
                } else {
                    return $redirect_url."?error=".$token_validate->message."&code=".$token_validate->status;
                }
            } else {
                return $redirect_url."?&error=error_in_server_response";
            }
        } else {
            return $redirect_url."?&error=api_validation_failed";
        }
    }

    /**
     * @internal moved from web.php
     * @since v3.4.0
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function errorInDatabaseConnectionView()
    {
        return view('errors.db');
    }

    /**
     * @internal moved from web.php
     * @since v3.4.0
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function unauthorizedView()
    {
        return view('errors.unauth');
    }

    /**
     * @internal moved from web.php
     * @since v3.4.0
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function boardOfflineView()
    {
        return view('errors.offline');
    }

    /**
     * @internal moved from web.php
     * @since v3.4.0
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function siteMap()
    {
        $sitemap = \App::make("sitemap");
        // Add static pages like this:
        $sitemap->add(\URL::to('/'), '2018-01-16T12:30:00+02:00', '1.0', 'daily');
        $sitemap->add(\URL::to('create-ticket'), '2018-11-16T12:30:00+02:00', '0.7', 'monthly');


        $sitemap->add(\URL::to('tags'), '2018-11-16T12:30:00+02:00', '0.7', 'monthly');
        $sitemap->add(\URL::to('auth/login'), '2018-11-16T12:30:00+02:00', '0.8', 'weekly');
        $sitemap->add(\URL::to('auth/register'), '2018-11-16T12:30:00+02:00', '0.8', 'weekly');
        $sitemap->add(\URL::to('password/email'), '2018-11-16T12:30:00+02:00', '0.8', 'weekly');

        $sitemap->add(\URL::to('knowledgebase'), '2018-11-16T12:30:00+02:00', '0.7', 'monthly');
        $sitemap->add(\URL::to('category-list'), '2018-11-16T12:30:00+02:00', '0.7', 'monthly');
        $sitemap->add(\URL::to('article-list'), '2018-11-16T12:30:00+02:00', '0.7', 'monthly');



        // Add dynamic pages of the site like this (using an example of this very site):


        // Add dynamic pages of the site like this (using an example of this very site):

        $categories = DB::table('kb_category')->where('status', '1')->orderBy('created_at', 'desc')->get();
        $articles=DB::table('kb_article')->where('status', '1')->where('visible_to', 'all_users')->orderBy('created_at', 'desc')->get();
        $pages=DB::table('kb_pages')->where('status', '1')->where('visibility', '1')->orderBy('created_at', 'desc')->get();


        if ($categories) {
            foreach ($categories as $category) {
                $sitemap->add(\URL::to("category-list/{$category->slug}"), $category->updated_at, '0.9', 'weekly');
            }
        }
        if ($articles) {
            foreach ($articles as $article) {
                $sitemap->add(\URL::to("show/{$article->slug}"), $article->updated_at, '0.9', 'weekly');
            }
        }
        if ($pages) {
            foreach ($pages as $page) {
                $sitemap->add(\URL::to("pages/{$page->slug}"), $page->updated_at, '0.9', 'weekly');
            }
        }
        // Now, output the sitemap:
        return $sitemap->render('xml');
    }

    /**
     * an empty post request which update X-XSRF cookie with latest csrf token, so that page doesn't expire
     * @since v3.4.0
     * @internal moved from web.php
     * @return \Illuminate\Http\JsonResponse
     */
    public function keepPageAlive()
    {
        return successResponse("new csrf token has been generated");
    }
}
