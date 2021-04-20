<?php

namespace App\FaveoLog\controllers;

use App\FaveoLog\Model\MailLog;
use App\FaveoLog\Model\CronLog;
use App\FaveoLog\Model\LogCategory;
use App\FaveoLog\Model\ExceptionLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Lang;
use Config;
use App\Model\helpdesk\Ticket\EmailThread;
use App\Model\helpdesk\Ticket\Tickets;
use Carbon\Carbon;
use App\Model\helpdesk\Email\Emails;
use Exception;

/**
 * Handles all read related operations fetching logs
 */
class LogViewController extends Controller
{

  public function __construct()
  {
    $this->middleware('role.admin');
  }

  /**
   * String that has to be searched
   * @var string
   */
  private $searchQuery;

  /**
   * 'asc' or 'desc'
   * @var string
   */
  private $sortOrder;

  /**
   * Field that has to be sorted
   * @var string
   */
  private $sortField;

  /**
   * Start of created_at time range
   * @var string
   */
  private $createdAtStart;

  /**
   * End of created_at time range
   * @var string
   */
  private $createdAtEnd;

  /**
   * Category ids corresponding to LogCategory table
   * @var array
   */
  private $categoryIds;

  /**
   * Populates request parameters into class properties
   * @param  Request $request
   * @return null
   */
  private function populateRequestParameters(Request $request)
  {
      $this->searchQuery = ($request->input('search-query')) ? $request->input('search-query') : ''; 
      
      $this->sortOrder = $request->sort_order ? $request->sort_order : 'desc';

      // if sort field is category, make sort field as category.name
      $this->sortField = $this->getSortField($request->sort_field);

      $this->limit = $request->limit ? $request->limit : 10;

      $this->createdAtStart = $this->getTimeInUTC($request->created_at_start);
      $this->createdAtEnd = $this->getTimeInUTC($request->created_at_end);
      $this->categoryIds = $this->getArrayFormat($request->category_ids);
  }

  /**
   * Gives an array of values based if comma seperated is provided.
   * @param  string|array $value
   * @return array
   */
  private function getArrayFormat($value) : array
  {
    if(!$value){
      return [];
    }

    if(!is_array($value)){
      return explode(',', $value);
    }

    return $value;
  }

  /**
   * Formats sort field in a way so that it can handle sort on joins also for category table
   * @param  string $field
   * @return string
   */
  private function getSortField($field)
  {
    if(!$field){
      return 'updated_at';
    }

    if($field == 'category'){
      return 'category.name';
    }

    return $field;
  }

  /**
   * gets logs
   * @param  string $type  type can be 'mail','cron','exception'
   * @return Response
   */
  public function getLogs($type, Request $request)
  {
    try{
          $this->populateRequestParameters($request);

          switch ($type) {

          case 'mail':
            return successResponse('', $this->getMailLogs($request));

          case 'cron':
            return successResponse('', $this->getCronLogs($request));

          case 'exception':
            return successResponse('', $this->getExceptionLogs());

          default:
            return successResponse('', $this->getMailLogs());
        }
    } catch(Exception $e){
      return errorResponse($e->getMessage());
    }
  }

    /**
     * Gives list of cron logs
     * @param $request
     * @return Response
     */
  private function getCronLogs($request)
  {
      $baseQuery = CronLog::with('exception')->where(function($q){
              $q->where('command', 'LIKE', "%$this->searchQuery%")
                  ->orWhere('description', 'LIKE', "%$this->searchQuery%")
                  ->orWhere('status', 'LIKE', "%$this->searchQuery%");
          })->orderBy($this->sortField, $this->sortOrder);

      $this->applyCommonFilters($baseQuery);


      $cronLogs = $baseQuery->paginate($this->limit);

      return $cronLogs;
  }

  /**
   * Gives list of mail logs
   * @return Response
   */
  private function getMailLogs($request)
  {
      // need to handle it by user also
      $baseQuery = MailLog::join('log_categories as category', 'category.id','=','mail_logs.log_category_id')
          ->with('category:id,name', "exception")
          ->where(function($q2){
            $q2->whereHas('category', function($q1){
              $q1->where('name','LIKE',"%$this->searchQuery%");
            })->orWhere(function ($q) {
                $q->where('sender_mail', 'LIKE', "%$this->searchQuery%")
                ->orWhere('reciever_mail', 'LIKE', "%$this->searchQuery%")
                ->orWhere('subject', 'LIKE', "%$this->searchQuery%")
                ->orWhere('body', 'LIKE', "%$this->searchQuery%")
                ->orWhere('source', 'LIKE', "%$this->searchQuery%")
                ->orWhere('status', 'LIKE', "%$this->searchQuery%")
                ->orWhere('collaborators', 'LIKE', "%$this->searchQuery%");
            });
          })->select('mail_logs.id','log_category_id','sender_mail','reciever_mail', "collaborators",
            'referee_id','referee_type','subject','source','mail_logs.created_at', 'mail_logs.updated_at','status', "exception_log_id")
          ->orderBy($this->sortField, $this->sortOrder);

      $this->applyCommonFilters($baseQuery);

      // sender mails filter
      $this->appendFilterQueryForArray($baseQuery, 'sender_mail', $request->sender_mails);

      // reciever mails filter
      $this->appendFilterQueryForArray($baseQuery, 'reciever_mail', $request->reciever_mails);

      $mailLogs = $baseQuery->paginate($this->limit);

      return $mailLogs;
  }

  /**
   * gets list of exception logs
   * @return Response
   */
  private function getExceptionLogs()
  {
      $baseQuery = ExceptionLog::join('log_categories as category', 'category.id','=','exception_logs.log_category_id')
          ->with('category:id,name')
          ->where(function($q2){
            $q2->whereHas('category', function($q1){
              $q1->where('name','LIKE',"%$this->searchQuery%");
            })->orWhere(function ($q) {
              $q->where('file', 'LIKE', "%$this->searchQuery%")
              ->orWhere('line', 'LIKE', "%$this->searchQuery%")
              ->orWhere('trace', 'LIKE', "%$this->searchQuery%")
              ->orWhere('message', 'LIKE', "%$this->searchQuery%");
            });
          })->orderBy($this->sortField, $this->sortOrder);

      $this->applyCommonFilters($baseQuery);

      $exceptionLogs = $baseQuery->paginate($this->limit);

      return $exceptionLogs;
  }

  /**
   * Applies default filter (created_at and category_id) to base query
   * @param  QueryBuilder $parentQuery
   * @return null
   */
  private function applyCommonFilters(&$parentQuery)
  {
    // category id filter
    $this->appendFilterQueryForArray($parentQuery, 'log_category_id', $this->categoryIds);

    // created_at_start filter
    $this->appendFilterQueryWithAndLogic($parentQuery, 'created_at', $this->createdAtStart, '>=');

    // created_at_end filter
    $this->appendFilterQueryWithAndLogic($parentQuery, 'created_at', $this->createdAtEnd, '<=');
  }

  /**
   * converts timezone from logged in user timezone into UTC
   * @return string
   */
  private function getTimeInUTC($time)
  {
    if(!$time){
      return null;
    }

    //covert given timestamp from agent timezone to UTC
    $agentTimeZone = agentTimeZone();

    //end time in UTC
    return changeTimezoneForDatetime($time, $agentTimeZone, 'UTC');
  }

  /**
   * Appends query to parent query
   * @param  QueryBuilder $parentQuery
   * @param  string $comparisionOperator '=','>=','<=','!=' etc
   * @param  string $fieldName           name of the field to be queried with
   * @param  string|null|int $fieldValue          value of the field
   * @return null
   */
  private function appendFilterQueryWithAndLogic(&$parentQuery, $fieldName, $fieldValue, $comparisionOperator = '=')
  {
    if($fieldValue){
      $parentQuery = $parentQuery->where($fieldName, $comparisionOperator, $fieldValue);
    }
  }

  /**
   * Appends query to parent query
   * @param  QueryBuilder $parentQuery
   * @param  string $fieldName           name of the field to be queried with
   * @param  string|null|int $fieldValue          value of the field
   * @return null
   */
  private function appendFilterQueryForArray(&$parentQuery, $fieldName, $fieldValue)
  {
    if($fieldValue){
      $fieldValue = $this->getArrayFormat($fieldValue);
      $parentQuery = $parentQuery->whereIn($fieldName, $fieldValue);
    }
  }

  /**
   * Gets the list of categories available
   * @param  Request $request
   * @return Response
   */
  public function getLogCategoryList(Request $request)
  {
      $this->populateRequestParameters($request);

      $categories = LogCategory::where('name','LIKE', "%$this->searchQuery%")
        ->simplePaginate($this->limit);

      return successResponse('', $categories);
  }

  /**
   * Gets list of mail body
   * @param  int $id Id of the log
   * @return Response
   */
  public function getMailBody($logId)
  {
      $mailLog = MailLog::where('id', $logId)->first();

      $message = $this->getMailResponseMessage($mailLog);

      return successResponse($message , ['mail_body' => $mailLog->body]);
  }

  /**
   * Handles mail response message based on category name passed
   * @param   $mailLog [description]
   * @return string
   */
  private function getMailResponseMessage(MailLog $mailLog) : string
  {
    $category = $mailLog->category()->first()->name;

    switch ($category) {
      case 'mail-fetch':
          return $this->getFetchMailResponseMessage($mailLog->referee_id);

      default:
          return '';
    }
  }

  /**
   * Checks the fetch mail and formats the message accordingly
   * @return string
   */
  private function getFetchMailResponseMessage(string $messageId) : string
  {
    $ticketId = EmailThread::where('message_id', $messageId)->value('ticket_id');

    if($ticketId){
      // getting ticket number
      $ticketNumber = Tickets::where('id', $ticketId)->value('ticket_number');

      // if ticket is found with this email give a ticket link
      // a ticket is found with this email
      $ticketLink = '<a href='.Config::get('app.url').'/thread/'.$ticketId.' target=_blank>#'.$ticketNumber.'</a>';

      return Lang::get('log::lang.ticket_found_with_this_mail', ['ticketId'=>$ticketLink]);
    }

    return Lang::get('log::lang.ticket_not_found_with_this_mail');
  }

  /**
   * Gets user by its email
   * @param Request
   * @return Response
   */
  public function getUserByEmail(Request $request)
  {
      $email = $request->email;
      $user = User::where('email', $email)->select('id','first_name','last_name','email','user_name','role','profile_pic')->first();
      if(!$user){
        // check for system configured mail
        $systemConfiguredMail = Emails::where('email_address', $email)->select('id','email_address as email', 'email_name as name')->first();
        if(!$systemConfiguredMail){
          return errorResponse(Lang::get('log::lang.no_user_created_with_this_email'));
        }
        $systemConfiguredMail->profile_pic = assetLink('image','system');
        $systemConfiguredMail->role = Lang::get('lang.system_configured_mail');
        $systemConfiguredMail->profile_link = Config::get('app.url').'/emails/'.$systemConfiguredMail->id.'/edit';
        return successResponse('',['user'=>$systemConfiguredMail]);
      }
      $user['name']= $user->full_name;
      $user['profile_link'] = Config::get('app.url').'/user/'.$user->id;
      unset($user['first_name'], $user['last_name']);
      return successResponse('', ['user' => $user]);
  }

  /**
   * Deletes logs in a given time interval
   * @param Request $request
   * @return
   */
  public function deleteLogs(Request $request)
  {
    $categories = $request->categories ? $request->categories : ['mail'];

    $deleteBefore = $this->getTimeInUTC($request->delete_before);

    $deleteAfter = $this->getTimeInUTC($request->delete_after);

    $this->deleteLogsByDate($categories, $deleteBefore, $deleteAfter);

    return successResponse(Lang::get('lang.deleted_successfully'));
  }

  /**
   * Deletes the log based on category, start time and end time
   * @param  string $category
   * @param  string $deleteBefore
   * @param  string $deleteAfter
   * @param  string $deleteAll
   * @return bool
   */
  public function deleteLogsByDate($categories, $deleteBefore, $deleteAfter = null)
  {
    $deleteAfter = !$deleteAfter ? Carbon::createFromTimestamp(0)->toDateString() : $deleteAfter;

    foreach ($categories as $category) {
      // get a parameter which deletes all logs
      $baseQuery = $this->getModelBasedOnCategory($category);

      if($deleteBefore){
        $baseQuery = $baseQuery->where('created_at', '<', $deleteBefore);
      }

      if($deleteAfter){
        $baseQuery = $baseQuery->where('created_at','>',$deleteAfter);
      }

      $baseQuery->delete();
    }
  }

    /**
     * Gets Log Model based on category
     * @param $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
  private function getModelBasedOnCategory($category)
  {
      switch ($category) {
        case 'mail':
          return MailLog::query();

        case 'cron':
          return CronLog::query();

        case 'exception':
          return ExceptionLog::query();

        default:
          return MailLog::query();
      }
  }
}
