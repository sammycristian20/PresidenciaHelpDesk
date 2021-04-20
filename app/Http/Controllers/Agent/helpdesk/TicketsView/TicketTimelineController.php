<?php
namespace App\Http\Controllers\Agent\helpdesk\TicketsView;

use App\Http\Controllers\Common\Dependency\DependencyDetails;
use App\Http\Controllers\Utility\FormController;
use App\Http\Requests\helpdesk\Ticket\TicketDetailsRequest;
use App\Traits\ClickableTicketParam;
use Closure;
use jamesiarmes\PhpEws\Response;
use Lang;
use App\Model\helpdesk\Ticket\Ticket_Thread as TicketThread;
use App\Model\helpdesk\Ticket\Tickets;
use App\Model\helpdesk\Filters\Filter;
use App\Http\Controllers\Agent\helpdesk\TicketsView\TicketsCategoryController;
use Illuminate\Http\Request;
use App\Model\helpdesk\Filters\Label;
use App\Model\helpdesk\Filters\Tag;
use App\Model\helpdesk\Settings\Plugin;
use DB;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\User;
use App\Model\helpdesk\Agent\Teams;
use App\Traits\EnhancedDependency;
use Crypt;
use Event;
use App\Model\helpdesk\Ratings\RatingRef;
use App\Model\helpdesk\Ratings\Rating;
use App\Model\helpdesk\Form\FormField;
use App\Http\Controllers\Client\helpdesk\ClientTicketController;
use Exception;

/**
 * handles ticket timeline data, mainly ticket specific details and ticket threads
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class TicketTimelineController extends TicketsCategoryController
{
    use ClickableTicketParam;

    /**
     * Gets ticket specific details of the passed ticketId
     * @param integer $ticketId Id of the required ticket
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function ticketDetails($ticketId)
    {
        //query for allowed ticket by logged in user
        $allowedTicketQuery = $this->allTicketsQuery();

        Event::dispatch('timeline-query-build', [$allowedTicketQuery]);

        //building baseQuery for the ticket
        $baseQuery = $allowedTicketQuery->where('id', $ticketId)->select('id', 'ticket_number', 'user_id', 'creator_id', 'dept_id', 'team_id', 'priority_id', 'sla', 'help_topic_id', 'assigned_to', 'source', 'isanswered', 'status', 'type', 'created_at', 'updated_at', 'duedate', 'location_id');

        $ticket = $this->getForeignKeyDataForTicket($baseQuery);

        if (!$ticket) {
            return errorResponse(Lang::get('lang.no-ticket-found'));
        }

        $ticket = $this->appendHyperlink($ticket);

        $formattedTicket = $this->formatTicket($ticket)->toArray();

        $this->appendAverageRatings($formattedTicket);

        // this data can be appended to add more fields to it
        Event::dispatch('timeline-data-dispatch', [&$formattedTicket]);

        return successResponse('', ['ticket' => $formattedTicket]);
    }


    /**
     * gets count for tasks
     * @param integer $ticketId
     * @return integer|null           null if calender plugin is off else integer count og tasks for current ticket
     */
    private function taskCount($ticketId)
    {
        $calender = Plugin::where('name', 'Calendar')->where('status', 1)->first();
        if ($calender) {
            return DB::table('tasks')->where([['ticket_id', $ticketId],['status', '!=', 'Closed']])->count();
        }
        return null;
    }

    /**
     * Gets foriegn key data for ticket. (this method is ticketDetails specific and should not be used by other methods.)
     * @param object $ticketQuery     BaseQuery for the ticket.
     * @return QueryBuilder
     */
    private function getForeignKeyDataForTicket($ticketQuery)
    {
        $ticketQuery = $ticketQuery->with(['statuses:id,name', 'types:id,name', 'priority:priority_id,priority as name,priority_color', 'departments:id,name',
            'slaPlan:id,name', 'departments:id,name', 'sources:id,name',
            'labels:labels.id,ticket_id,value as name,color',
            'tags:tags.id,ticket_id,value as name',
            'user:id,first_name,last_name,user_name,email,profile_pic',
            'creator:id,first_name,last_name,user_name,email',
            'assignedTeam:id,name', 'helptopic:id,topic as name',
            'assigned:id,first_name,last_name,email','location:id,title as name',
            'firstThread:id,ticket_id,title',
            'user.organizations:organization.id,name',
            'lastThread' => function ($q) {
                $q->select('ticket_id', 'user_id')->with('user:id,first_name,last_name,user_name,email');
            },
            'collaborator' => function ($q) {
                $q->select('id', 'ticket_id', 'user_id')->with('userBelongs:id,first_name,last_name,user_name,email,profile_pic');
            },
          ]);
        $ticketQuery = $this->customFieldQueryForTicket($ticketQuery);

        $ticket = $ticketQuery->first();

        /**
         * @todo: find a way to do a direct query distant relation (hasManyThrough won't work in this scenario because pivot table exists in bw)
         * https://laracasts.com/discuss/channels/eloquent/hasmanythrough-with-pivot?page=0
         *
         * @author avinash kumar <avinash.kumar@ladybirdweb.com?
         */
        if ($ticket) {
            $ticket->organizations = $ticket->user->organizations;
            unset($ticket->user->organizations);
        }
        
        return $ticket;
    }

    /**
     * Append average ratings to tickets array
     * @param array $ticket
     * @return null
     * @author avinash.kumar <avinash.kumar@ladybirdweb.com>
     */
    private function appendAverageRatings(array &$ticket)
    {
        // query each rating where thread id is there and find and average of it and give it to frontnend
        // piece of shit
        // first go to ratings table, check for comment area. Then get all ids of ratings where
        // area is comment area
        // put a department check also
        $ticketId = $ticket['id'];

        $departmentName = $ticket['departments']['name'];

        // first get all the ratings in comment area
        $ratingIds = Rating::where('rating_area', 'Comment Area')
          ->where(function ($q) use ($departmentName) {
              $q->where('restrict', $departmentName)->orWhere('restrict', "")->orWhere('restrict', "General");
          })->orWhere('rating_area', '!=', 'Comment Area')->pluck('id')->toArray();

        $averageRatings = [];

        // loop over it and find average and push the object in the target array
        foreach ($ratingIds as $id) {
            // if not found it should send an empty array anyway
            $ratingRefs = RatingRef::where('ticket_id', $ticketId)->where('rating_id', $id)->pluck('rating_value')->toArray();

            $averageRating = count($ratingRefs) ? array_sum($ratingRefs)/count($ratingRefs) : 0;
            $averageRatingObject = new RatingRef(['rating_id' => $id, 'rating_value' => $averageRating,'ticket_id' => $ticketId]);
            $averageRatings[] = $averageRatingObject->toArray();
        }

        // now change the values to Helpdesk Area so that frontend can handle it
        $ticket['ratings'] = $averageRatings;
    }

    /**
     * Formats the passed ticket by removing paramters that are not reuired in the final response
     * @param Tickets $ticket          Ticket data
     * @return Tickets
     */
    private function formatTicket(&$ticket)
    {
        $ticket->title = $ticket->firstThread->title;
        $ticket->last_replier = $ticket->lastThread->user;

        $this->formatCollaborators($ticket);

        $ticket->source = $ticket->sources;

        $ticket->assignee =  $ticket->team_id ? $ticket->assignedTeam : $ticket->assigned;

        if ($ticket->assignee) {
            $ticket->assignee = (object)$ticket->assignee->toArray();
            $ticket->assignee->name = isset($ticket->assignee->name) ? $ticket->assignee->name : $ticket->assignee->full_name;
            $ticket->assignee->type = $ticket->team_id ? 'team': 'agent';
        }

        unset(
            $ticket->user_id,
            $ticket->team_id,
            $ticket->dept_id,
            $ticket->priority_id,
            $ticket->sla,
            $ticket->type,
            $ticket->help_topic_id,
            $ticket->assigned_to,
            $ticket->firstThread,
            $ticket->lastThread,
            $ticket->sources,
            $ticket->location_id,
            $ticket->assigned,
            $ticket->assignedTeam
        );

        $ticket->status = $ticket->statuses;
        $ticket->type = $ticket->types;
        $ticket->priority->id = $ticket->priority->priority_id;
        $ticket->task_count = $this->taskCount($ticket->id);
        $ticket->user->name = $ticket->user->meta_name;

        $ticket->is_overdue = Tickets::isOverdue($ticket->duedate);
        $ticket->due_today = Tickets::isDueToday($ticket->duedate, agentTimeZone());
        unset($ticket->statuses, $ticket->types, $ticket->priority->priority_id);

        //formatting custom fields
        $this->formatCustomFields($ticket);

        return $ticket;
    }

    /**
     * formats collaborators in ticket and unsets unncessary fields
     * @param Tickets &$ticket    $ticket with collaborators
     * @return null
     */
    private function formatCollaborators(Tickets &$ticket)
    {
        $collaborators = [];

        foreach ($ticket->collaborator as $collaborator) {
            $collaborator->userBelongs->name = $collaborator->userBelongs->meta_name;
            $collaborator->userBelongs->collaborator_id = $collaborator->id;
            array_push($collaborators, $collaborator->userBelongs);
        }

        $ticket->collaborators = $collaborators;

        unset($ticket->collaborator);
    }

    /**
     * formats custom field by removing custom field entries where label is null(uses pass by reference)
     * @param Tickets &$ticket  ticket pass by reference
     * @return null
     */
    private function formatCustomFields(Tickets &$ticket)
    {
        foreach ($ticket->customFieldValues as $formData) {
            unset($formData->custom_id, $formData->custom_type);
        }
    }

    /**
     * Appends custom form field query to the passed query
     * @param  QueryBuilder $ticketQuery  base query for ticket
     * @return QueryBuilder               query after appending custom
     */
    private function customFieldQueryForTicket(QueryBuilder $ticketQuery)
    {
        // get custom form data and append it in id, label and value
        return $ticketQuery->with('customFieldValues:id,form_field_id,value,custom_id,custom_type');
    }

    /**
     * Returns all the threads of ticketId passed
     * @param integer $ticketId     Id of the asked ticket
     * @param object $request
     * @return array                Threads if success else failure response
     */
    public function ticketThreads($ticketId, Request $request)
    {
        $allowedTickets = $this->allTicketsQuery();
        $limit = $request->input('limit') ? $request->input('limit') : 10;

        //checking ticket in user's ticket access domain
        $ticket = $allowedTickets->where('id', $ticketId)->select('id', 'dept_id', 'created_at')->first();


        //if ticket is not found in user's access domain
        if (!$ticket) {
            return errorResponse(Lang::get('lang.no-ticket-found'));
        }

        //relationship is one to one but relation
        $department = $ticket->department->name;

        $threadData = TicketThread::where('ticket_id', $ticketId)
                ->select('id', 'user_id', 'title', 'body', 'thread_type', 'created_at', 'is_internal')
                ->with('attach:id,name,size,thread_id,content_id,poster,path')
                ->with('user:id,first_name,last_name,email,user_name,role,profile_pic')
                ->with([
                  'ratings' => function ($q) use ($department) {
                      $q->whereHas('ratingType', function ($q2) use ($department) {
                          $q2->select('id', 'name', 'rating_scale')->where(function ($q2) use ($department) {
                              $q2->where('restrict', $department)->orWhere('restrict', "")->orWhere('restrict', "General");
                          });
                      });
                  }])->orderBy('id', 'DESC')->paginate($limit)->toArray();

        //toArray is required to format pagination, else can't handle

        $threadData['threads'] = $threadData['data'];

        //appending ticket's created at time to the threadData object
        $threadData['ticket_created_at'] = $ticket['created_at']->format('Y-m-d h:i:s');

        $threadData['threads'] = $this->getIconDetailsForThreadCreator($threadData['threads']);

        unset($threadData['data'], $threadData['first_page_url'], $threadData['last_page_url'], $threadData['next_page_url'], $threadData['prev_page_url'], $threadData['path']);

        return successResponse('', $threadData);
    }

    /**
     * gets name based on the passed userData.
     * @param object|array $userData        User data with information regarding his first_name, last_name and user_name OR (null or emptyarray)
     *                                      if null is given name will be returned as 'System'
     * @return string                       Name
     */
    private function getName($userData)
    {
        if (!$userData || !count($userData)) {
            return 'System';
        } else {
            $name = $userData['first_name'] ? $userData['first_name'] . ' ' . $userData['last_name'] : $userData['user_name'];
            return $name;
        }
    }

    /**
     * Appends icon details to ticket threads based on the person( 'user','agent/admin' or null (is_internal) ) who created it.
     * NOTE: It is made only because front-end asked for. It serves no special purpose and should not be re-used
     * @param $threads    Ticket with threads
     * @return
     */
    private function getIconDetailsForThreadCreator($threads)
    {
        $formattedThread = [];
        foreach ($threads as $thread) {
            $user = $thread['user'];
            unset($thread['user'], $thread['user_id']);
            $thread['user'] = [];
            $thread['user']['name'] = $this->getName($user);
            if ($thread['is_internal']) {
                $thread['icon_class'] = $user ? (
                    ($thread['thread_type'] == 'note') ? 'fa fa-comment bg-gray' : 'fa fa-history bg-gray'
                    ) : 'fa fa-tag bg-gray';
                $thread['posted_by'] = ($thread['thread_type'] == 'note') ? 'Agent' : 'System';
                $thread['user']['profile_pic'] = $user ? $user['profile_pic'] : assetLink("image", "system");
                $thread['user']['id'] = null;
            } else {
                $thread['icon_class'] = ($user['role'] == 'user') ? 'fa fa-comment bg-aqua' : 'fa fa-comments-o bg-yellow';
                $thread['posted_by'] = ($user['role'] == 'user') ? 'Customer' : 'Agent';
                $thread['user']['id'] = $user['id'];
                $thread['user']['profile_pic'] = $user['profile_pic'];
            }
            array_push($formattedThread, $thread);
        }
        return $formattedThread;
    }

    /**
     * Gets ticket specific details of the passed ticketId in edit mode
     * @param integer $ticketId       Id of the required ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function ticketEditFormWithValue(Request $request, $ticketId)
    {
        //query for allowed ticket by logged in user
        $allowedTicketQuery = $this->allTicketsQuery();

        //building baseQuery for the ticket
        $ticket = $allowedTicketQuery->where('id', $ticketId)
            ->select('id', 'user_id', 'dept_id', 'priority_id', 'sla', 'help_topic_id', 'assigned_to', 'source', 'status', 'type', 'location_id')
            ->with([
                'statuses:id,name','types:id,name',
                'priority:priority_id,priority as name',
                'slaPlan:id,name','sources:id,name',
                //make this as requester
                'user:id,user_name,email,first_name,last_name,email,profile_pic',
                'assigned:id,user_name,email,first_name,last_name,email,profile_pic',
                'firstThread:id,ticket_id,title',
                'location:id,title as name',
                'collaborator' => function ($q) {
                    $q->select('id', 'ticket_id', 'user_id')->with('userBelongs:id,first_name,last_name,user_name,email,profile_pic');
                },
                'customFieldValues',
            ])->first();

        $this->appendHelptopicDepartmentChildFields($ticket);

        $ticket = $ticket->toArray();

        $this->formatTicketDetailsForEditMode($ticket);

        $editForm = (new FormController())->getFormWithEditValues($ticket, 'ticket', $request->input('scenario', 'edit'), 'agent', $ticketId);

        return successResponse('', $editForm);
    }

    /**
     * Appends helptopic and department child fields (plus conversion of group fields to form fields)
     * @param  Tickets $ticket
     * @return null
     */
    private function appendHelptopicDepartmentChildFields(Tickets &$ticket)
    {
        $dependencyDetails = new DependencyDetails();
        $dependencyDetails->setOutputAsObject(true);
        $ticket->help_topic = $dependencyDetails->getDependencyDetails('help_topic_id', $ticket->help_topic_id, true, false, ['scenario'=>'edit']);
        $ticket->department = $dependencyDetails->getDependencyDetails('department_id', $ticket->dept_id, true, false, ['scenario'=>'edit']);
    }

    /**
     * Formats ticket in edit mode
     * @param  array &$ticket ticket object
     * @return null
     */
    private function formatTicketDetailsForEditMode(&$ticket)
    {
        foreach ($ticket['custom_field_values'] as &$field) {
            $ticket['custom_'.$field['form_field_id']] = $field['value'];
        }

        unset($ticket['status'], $ticket['type'], $ticket['source']);

        $ticket['requester'] = $ticket['user'];
        $ticket['subject'] = $ticket['first_thread']['title'];
        $ticket['status'] = $ticket['statuses'];
        $ticket['priority']['id'] = $ticket['priority']['priority_id'];
        $ticket['source'] =  $ticket['sources'];
        $ticket['type'] = $ticket['types'];

        if ($ticket['requester']) {
            // should give username/email
            $requester = $ticket['requester'];
            $ticket['requester']['name'] = $ticket['requester']['meta_name'];
        }

        if ($ticket['assigned']) {
            $ticket['assigned']['name'] = $ticket['requester']['meta_name'];
        }

        unset(
            $ticket['custom_field_values'],
            $ticket['departments'],
            $ticket['user'],
            $ticket['first_thread'],
            $ticket['user_id'],
            $ticket['dept_id'],
            $ticket['priority_id'],
            $ticket['sla'],
            $ticket['help_topic_id'],
            $ticket['assigned_to'],
            $ticket['sources'],
            $ticket['thread_count'],
            $ticket['collaborator_count'],
            $ticket['types'],
            $ticket['priority']['priority_id'],
            $ticket['attachment_count'],
            $ticket['poster'],
            $ticket['statuses'],
            $ticket['helptopic'],
            $ticket['location_id']
        );
    }

    /**
     * gets conversation by hash
     * @param  string  $hash
     * @param  Request $request
     * @return null
     */
    public function getTicketConversationByHash($hash, Request $request)
    {
        try {
            $ticketId = Crypt::decryptString($hash);

            $currentPage = $request->input('page') ? $request->input('page') : 1;
          // get information in ticket
            $ticket = (new ClientTicketController)->getTicketById($ticketId, $currentPage);

            return successResponse('', ['ticket'=>$ticket]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Returns all the threads of ticketId passed
     * @param integer $ticketId     Id of the asked ticket
     * @param object $request
     * @return array                Threads if success else failure response
     */
    public function ticketConversationForAgent($ticketId, TicketDetailsRequest $request)
    {
        $limit = $request->input('limit') ?: 10;
        // relationship is one to one but relation
        // RATING modules is stores name of the department for mapping it with department name
        // TODO: remove this after rating modules is rewritten
        $department = Tickets::whereId($ticketId)->select('id', 'dept_id')->first()->department->name;
        /**
         * Jugaad 999
         *
         * In ancient times ticket activities were stored in the ticket_thread table itself.
         * Since new implementation of tickt activity log does not use and save such
         * records anymore but until the notifications are corrected system will still
         * save these activty records.
         *
         * This makeshifter was deployed to achieve the following goals
         * - Keep our ancestor happy by showing them those old activty records saved on old
         * ticket which were created/updated before the new activity log was released in v4.3
         *
         * - Making sure all new activity records saved in ticket_thread table can be discarded
         * after notification corrections are done and extra code in quering the threads can be
         * removed.
         */
        $updatedToV430At =  commonSettings('v_4_3_0_update', '');
        $threadData = TicketThread::where('ticket_id', $ticketId)
            // querying only conversation threads and internal notes
            // @todo: activity itself should be removed while creating the ticket (but removing it is causing notifications to fail, so hiding it instead)
            ->where(function ($q) use($updatedToV430At){
                $q->where('is_internal', '!=', 1)->orWhere('thread_type', 'note')
                ->when($updatedToV430At, function($q) use($updatedToV430At) {
                  $q->orWhere('created_at', '<', $updatedToV430At);
                });
            })
            ->select('id', 'user_id', 'title', 'body', 'created_at', 'is_internal', 'poster', 'thread_type')
            ->with('attach:id,name,size,thread_id,content_id,poster,path')
            ->with('user:id,first_name,last_name,email,user_name,role,profile_pic')
            ->with(['ratings' => function ($q) use ($department) {
                    $q->whereHas('ratingType', function ($q2) use ($department) {
                        $q2->select('id', 'name', 'rating_scale')->where(function ($q2) use ($department) {
                            $q2->where('restrict', $department)->orWhere('restrict', "")->orWhere('restrict', "General");
                        });
                    });
            }])->orderBy('id', 'DESC')->paginate($limit);


        $threadData->getCollection()->transform(function ($element) {
            $iconDetails = $this->getThreadTypeIconDetails($element);
            $element->thread_type_icon_class = $iconDetails->icon_class;
            $element->thread_type_text = $iconDetails->thread_type_text;

            // "user" is a relation and cannot be simply assigned to a value,
            // thats why first converting into a stdObj and then assigning the value
            $formattedUser = $this->getFormattedUser($element->user);
            $element = (object) $element->toArray();
            $element->user = $formattedUser;

            // these things has been queried for relationships to work OR for computational purposes
            // and can be unset after results are queried
            unset($element->user_id, $element->poster);
            return $element;
        });

        return successResponse('', $threadData);
    }

    /**
     * Gets icon details of the thread in this format {icon_class: 'fa fa-comment bg-gray', thread_type_text: 'Posted by agent as Internal Note'}
     * @param TicketThread $thread
     */
    private function getThreadTypeIconDetails(TicketThread $thread)
    {
        if ($thread->poster == 'client') {
            return (object)['icon_class'=> 'fa fa-comment bg-blue', 'thread_type_text' => Lang::get('lang.posted_by_client')];
        }

        if ($thread->thread_type == 'note' && $thread->is_internal) {
            return (object)['icon_class'=> 'fa fa-sticky-note bg-gray', 'thread_type_text' => Lang::get('lang.posted_as_internal_note')];
        }

        if ($thread->poster == 'support' && !$thread->is_internal) {
            return (object)['icon_class'=> 'fa fa-comment bg-yellow', 'thread_type_text' => Lang::get('lang.posted_by_agent')];
        }

        return (object)['icon_class'=> 'fa fa-desktop bg-gray', 'thread_type_text' => Lang::get('lang.posted_by_system')];
    }

    /**
     * Gets formmtted user in "id", "name" format. If user is null, it is formatted to make system user
     * @param User|null $user
     * @return object
     */
    private function getFormattedUser(User $user = null)
    {
        if (!$user) {
            return (object)['id'=> null, 'name'=> Lang::get("lang.system"), 'profile_pic'=> assetLink("image", "system")];
        }

        $user->name = $user->full_name;

        return $user;
    }

    /**
     * Appends hyperlink to ticket fields
     * @param $ticket
     * @return Tickets
     * @throws Exception
     */
    protected function appendHyperlink(&$ticket) : Tickets
    {
        // pass key, value and key in the relation and relationName and ticket
        $this->formattedHyperlink = false;

        $this->updateDependencyHyperlink('status-ids', $ticket->status, $ticket, 'statuses', true, 'href');

        $this->updateDependencyHyperlink('sla-plan-ids', $ticket->sla, $ticket, 'slaPlan', true, 'href');

        $this->updateDependencyHyperlink('dept-ids', $ticket->dept_id, $ticket, 'departments', true, 'href');

        $this->updateDependencyHyperlink('priority-ids', $ticket->priority_id, $ticket, 'priority', true, 'href');

        $this->updateDependencyHyperlink('type-ids', $ticket->type, $ticket, 'types', true, 'href');

        $this->updateDependencyHyperlink('location-ids', $ticket->location_id, $ticket, 'location', true, 'href');

        $this->updateDependencyHyperlink('helptopic-ids', $ticket->help_topic_id, $ticket, 'helptopic', true, 'href');

        $this->updateDependencyHyperlink('source-ids', $ticket->source, $ticket, 'sources', true, 'href');

        $this->updateDependencyHyperlink('team-ids', $ticket->team_id, $ticket, 'assignedTeam', true, 'href');

        $this->updateUserHyperlink('assignee-ids', $ticket->assigned_to, $ticket, 'assigned', 'href');

        $this->updateUserHyperlink('owner-ids', $ticket->user_id, $ticket, 'user', 'href');

        $this->updateUserHyperlink('creator-ids', $ticket->creator_id, $ticket, 'creator', 'href');

        $this->updateArrayHyperlink('label-ids', $ticket, 'labels', 'href');

        $this->updateArrayHyperlink('tag-ids', $ticket, 'tags', 'href');

        $this->updateArrayHyperlink('organization-ids', $ticket, 'organizations', 'href');

        $this->appendCustomFieldHyperlinks($ticket);

        return $ticket;
    }

    /**
     * Appends custom field hyperlinks to ticket object
     * @param Tickets $ticket
     * @return Tickets
     */
    protected function appendCustomFieldHyperlinks(Tickets $ticket)
    {
        // custom field values
        foreach ($ticket->customFieldValues as &$item) {
            // if value is array, change the data-structure
            // ['href'=> 'link', value=>'value']
            if (is_array($item->value)) {
                $hrefArray = [];
                foreach ($item->value as $value) {
                    array_push($hrefArray, $this->getInboxUrl(). "&custom_$item->form_field_id=$value");
                }
                // to avoid indirect modification of a property (bug in php), not modifying $item->href in the loop
                // @see https://bugs.php.net/bug.php?id=41641
                $item->href = $hrefArray;
            } else {
                $item->href = $this->getInboxUrl(). "&custom_$item->form_field_id=$item->value";
            }
        }

        return $ticket;
    }
}
