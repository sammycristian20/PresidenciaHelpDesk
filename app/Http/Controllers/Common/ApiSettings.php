<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Model\Api\ApiSetting;
use App\Model\helpdesk\Ticket\Tickets;
use Exception;
use Log;

class ApiSettings extends Controller
{
    public function ticketDetailEvent($detail)
    {
        try {
            $ticket = new Tickets();
            $ticketid = $detail->ticket_id;
            $data = $ticket
                    ->join('ticket_thread', function ($join) use ($ticketid) {
                        $join->on('tickets.id', '=', 'ticket_thread.ticket_id')
                        ->where('ticket_thread.ticket_id', '=', $ticketid);
                    })
                    ->join('users', 'ticket_thread.user_id', '=', 'users.id')
                    ->select('ticket_thread.title', 'ticket_thread.body', 'users.first_name', 'users.last_name', 'users.email', 'ticket_thread.created_at')
                    ->get()
                    ->toJson();
            $this->postHook($data);
        } catch (Exception $ex) {
//            dd($ex);
            throw new Exception($ex->getMessage());
        }
    }

    public function postHook($data)
    {
        try {
            $set = ApiSetting::where('key', 'ticket_detail')->first();
            if ($set) {
                if ($set->value) {
                    $this->postForm($data, $set->value);
                }
            }
        } catch (Exception $ex) {
            // dd($ex);
            throw new Exception($ex->getMessage());
        }
    }

    public function postForm($data, $url)
    {
        try {
            $post_data = [
                'data' => $data,
            ];
            $upgrade_controller = new \App\Http\Controllers\Update\UpgradeController();
            $upgrade_controller->postCurl($url, $post_data);
            Log::info('ticket details has send to : '.$url.' and details are : '.$data);
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
}
