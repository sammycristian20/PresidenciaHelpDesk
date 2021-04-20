<?php

namespace App\Helper;

use App\Http\Controllers\Agent\helpdesk\TicketController;
use App\Http\Controllers\Agent\helpdesk\UserController;
use App\Http\Requests\helpdesk\Ticket\AgentPanelTicketRequest;
use App\Traits\UserVerificationHelper;
use App\User;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BatchTicketImport implements ToArray, WithHeadingRow
{
    use UserVerificationHelper;

    private $users_list = [];
    private $data;
    /**
     * @var TicketController
     */
    private $ticket;

    public function __construct($data)
    {
        $this->data = $data;
        $this->ticket = new TicketController();
    }
    public function array(array $array)
    {
        $this->createBatchTickets($array);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function createBatchTickets($results){

        foreach ($results as $key => $value) {
            if($user = User::where('email', $value['email'])
                ->where('email', '!=', null)->where('email','!=','')->first()){
                array_push($this->users_list, $user->id);
            }
            else{
                $isValidEmail = filter_var($value['email'], FILTER_VALIDATE_EMAIL);
                if(!$isValidEmail){
                    continue;
                }

                // Creating user in faveo if not found
                $request = new \Illuminate\Http\Request;
                $request->merge([
                    'email' => $value['email'],
                    'user_name' => $value['email'],
                    'first_name' => $value['first_name'],
                    'last_name' => $value['last_name'],
                    'role' => 'user',
                    'full_name' => $value['first_name']. ' '. $value['last_name']
                ]);

                $obj = new UserController(new \App\Http\Controllers\Common\PhpMailController);
                $new_user = $obj->createRequester($request, 'batch-ticket' );

                // NOTE: adding it so that it skips the user which gives exception in user creation
                // REASON: createRequester is an outdated method which returns a response, from which
                // getting the exception is not simple
                if(!is_array($new_user)){
                    continue;
                }
                $this->setEntitiesVerifiedByModel(User::find($new_user['id']));
                array_push($this->users_list, $new_user['id']);
            }
        }

        foreach ($this->users_list as $key => $value) {
            $this->data['requester'] = $value;

            // get all attachments from the request in the memory
            $request = new AgentPanelTicketRequest($this->data);
            $this->ticket->post_newticket($request);
        }

        try{
            $notify = [
                'message' => __('lang.batch-ticket-created-success'),
                'to'      => \Auth::user()->id,
                'by'      => \Auth::user()->id,
                'table'   => "",
                'row_id'  => "",
                'url'     => url('/tickets'),
            ];
            $email_data = [
                "content" => "Hi ".\Auth::user()->user_name. ",<br> <br>".__('lang.batch-ticket-created-success'),
                "subject" => __('lang.Ticket-created-successfully')
            ];
            \Event::dispatch('batch.ticket.notify', [$notify]);
            \Event::dispatch('batch.ticket.email.notify', [$email_data]);
        }
        catch(\Exception $e){
            \Log::info("Exception caught while sending batch ticket notify: ".$e);
        }
        // $this->notifyUser();
    }
}