<?php

namespace App\FaveoReport\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\helpdesk\Ticket\Ticket_Status;
use App\Model\helpdesk\Manage\Help_topic;
use App\Model\helpdesk\Agent\Teams;

class Utility extends Controller {

    public $request;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    /**
     * get  all agents
     * @return json
     */
    public function agentApi()
    {
        $term = $this->request->input('term');
        $inactive = $this->request->input('inactive', 'No');
        $items = \App\User::where('role', '!=', 'user')
                ->where(function($q) use($term) {
                    $q->orWhere('first_name', "LIKE", "%" . $term . "%")
                    ->orWhere('last_name', 'LIKE', "%" . $term . "%")
                    ->orWhere('email', 'LIKE', "%" . $term . "%")
                    ->orWhere('user_name', 'LIKE', "%" . $term . "%");
                })
                ->select('id','user_name as text','first_name','last_name','email','user_name','profile_pic');

        if ($inactive == 'No') {
            $items->where('active', 1);
        }                

        return  $items->get()->toArray();
    }


    /**
     * get all departments
     * @return json
     */
    public function departmentApi()
    {
        $search = $this->request->input('q');
        $items = \App\Model\helpdesk\Agent\Department::
                where('name', 'LIKE', "%" . $search . "%")
                ->select('id', 'name as text')
                ->get()
                ->toArray();

        return response()->json(compact('items'), 200);
    }


    /**
     * get all priority
     * @return json
     */
    public function priorityApi()
    {
        $search = $this->request->input('q');
        $inactive = $this->request->input('inactive', 'No');
        $priority = \App\Model\helpdesk\Ticket\Ticket_Priority::
                where('priority', 'LIKE', "%" . $search . "%")
                ->select('priority_id as id', 'priority as text');                

        if ($inactive == 'No') {
            $priority->where('status', 1);
        }

        $items = $priority->get()->toArray();

        return response()->json(compact('items'), 200);
    }


    /**
     * get all sources
     * @return json
     */
    public function sourceApi()
    {
        $search = $this->request->input('q');
        $items = \App\Model\helpdesk\Ticket\Ticket_source::
                where('name', 'LIKE', "%" . $search . "%")
                ->select('id', 'name as text')
                ->get()
                ->toArray();

        return response()->json(compact('items'), 200);
    }


    /**
     * get all clients
     * @return json
     */
    public function clientApi()
    {
        $term = $this->request->input('term');
        $inactive = $this->request->input('inactive', 'No');
        $items = \App\User::
                where(function($q) use($term ) {
                    $q->orWhere('first_name', "LIKE", "%" . $term . "%")
                    ->orWhere('last_name', 'LIKE', "%" .  $term . "%")
                    ->orWhere('email', 'LIKE', "%" .  $term . "%")
                    ->orWhere('user_name', 'LIKE', "%" .  $term . "%");
                })
                ->where('role', '=', 'user')
                ->select('id', 'user_name as text','first_name','last_name','email','user_name','profile_pic');

        if ($inactive == 'No') {
            $items->where('active', 1);
        } 

       return  $items->get()->toArray();

    }


    /**
     * get all types
     * @return json
     */
    public function typeApi()
    {
        $search = $this->request->input('q');
        $inactive = $this->request->input('inactive', 'No');
        $types = \App\Model\helpdesk\Manage\Tickettype::
                where('name', 'LIKE', "%" . $search . "%")
                ->select('id', 'name as text');                

        if ($inactive == 'No') {
            $types->where('status', 1);
        }

        $items = $types->get()->toArray();

        return response()->json(compact('items'), 200);
    }


    /**
     * get all status
     * @return json
     */
    public function statusApi()
    {
        $search = $this->request->input('q');
        $items = Ticket_Status::
                where('name', 'LIKE', "%" . $search . "%")
                ->select('id', 'name as text')
                ->get()
                ->toArray();
     
        return response()->json(compact('items'), 200);
    }


    /**
     * get all helptopic
     * @return json
     */
    public function helptopicApi()
    {
        $search = $this->request->input('q');
        $inactive = $this->request->input('inactive', 'No');
        $topics = Help_topic::
                where('topic', 'LIKE', "%" . $search . "%")
                ->select('id', 'topic as text');

        if ($inactive == 'No') {
            $topics->where('status', 1);
        }

        $items = $topics->get()->toArray();

        return response()->json(compact('items'), 200);
    }


    /**
     * get all team
     * @return json
     */
    public function teamApi()
    {
        $search = $this->request->input('q');
        $inactive = $this->request->input('inactive', 'No');
        $teams = Teams::
                where('name', 'LIKE', "%" . $search . "%")
                ->select('id', 'name as text');                

        if ($inactive == 'No') {
            $teams->where('status', 1);
        }

        $items = $teams->get()->toArray();

        return response()->json(compact('items'), 200);
    }

    /**
     * get  all users, admins and agents
     * @return json
     */
    public function creatorApi()
    {
        $term = $this->request->input('term');
        $inactive = $this->request->input('inactive', 'No');
        $items = \App\User::where(function($q) use($term) {
                    $q->orWhere('first_name', "LIKE", "%" . $term . "%")
                    ->orWhere('last_name', 'LIKE', "%" . $term . "%")
                    ->orWhere('email', 'LIKE', "%" . $term . "%")
                    ->orWhere('user_name', 'LIKE', "%" . $term . "%");
                })
                ->select('id','user_name as text','first_name','last_name','email','user_name','profile_pic');

        if ($inactive == 'No') {
            $items->where([['active', 1], ['is_delete', '<>', 1]]);
        }   

        return  $items->get()->toArray();
    }

}
