<?php

namespace App\Model\Common;

use App\BaseModel;
use App\Model\helpdesk\Ticket\Ticket_Thread as Thread;
use Config;

class Template extends BaseModel
{
    protected $table = 'templates';
    protected $fillable = ['name', 'message', 'type', 'variable', 'subject', 'set_id', 'template_category'];

    /**
     * Gets ticket thread template for the ticket conversation
     * @param  int $ticketId id of the ticket
     * @return string
     */
    public static function getTicketThreadsTemplate(int $ticketId) : string
    {
      $threads = Thread::where('ticket_id', $ticketId)
        ->where('is_internal', 0)
        ->with('user:id,first_name,last_name,email,user_name,profile_pic')
        ->select('user_id', 'title', 'body', 'created_at')
        ->orderBy('id','ASC')
        ->get();

      $body = "";
      foreach ($threads as $thread) {
        $user = $thread->user;
        $userProfilePath = Config::get('app.url').'/user'."/".$user->id;
        $threadBody = $thread->getOriginal("body");
        $createdAt = $thread->created_at . ' UTC';
        $body = $body . view('themes.default1.agent.helpdesk.ticket.ticketForward',
          compact('user', 'threadBody', 'userProfilePath', 'createdAt'));
      }
      return $body;
    }
}
