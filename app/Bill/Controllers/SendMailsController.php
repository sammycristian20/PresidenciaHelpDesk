<?php

namespace App\Bill\Controllers;

use App\Http\Controllers\Common\PhpMailController;

class SendMailsController extends PhpMailController
{
	public function handleMail($alert, $to, $scenario, $subject, $attachments = [], $variable = [], $directSend = false, $userId = null)
	{
        $from = $this->mailfrom('1', 0);
        $message = [
            'subject'  => $subject,
            'scenario' => $scenario,
            'attachments' => $attachments
        ];
        if ($directSend) {
            $this->sendmail($from, $to, $message, $variable);
        }
        $notification = new \App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController();
		$notifications[] = [
            $alert => [
                'from'        => $from,
                'message'     => $message,
                'variable'    => $variable,
                'userid'      => $userId
            ]
        ];
        $notification->setDetails($notifications);
	}
}