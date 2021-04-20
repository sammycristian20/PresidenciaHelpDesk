<?php


namespace App\Repositories;

use App\Model\Common\TicketActivityLog;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Structure\Activity;
use App\Traits\TicketKeyMutator;
use Auth;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TicketActivityLogRepository
{
    use TicketKeyMutator;

    /**
     * activity events allowed in ticket activities
     */
    const
        TICKET_CREATED = 'ticket_created',
        TICKET_UPDATED = 'ticket_updated',
        WORKFLOW_ENFORCED = 'workflow_enforced',
        LISTENER_ENFORCED = 'listener_enforced',
        SLA_ENFORCED = 'sla_enforced',
        REPLY_ADDED = 'reply_added',
        INTERNAL_NOTE_ADDED = 'internal_note_added',
        TICKET_FORWARDED = 'ticket_forwarded',
        TICKET_FORKED = 'ticket_forked';

    /**
     * Action taker types
     */
    const USER = 'user', WORKFLOW = 'workflow', LISTENER = 'listener', SLA = 'sla';

    /**
     * Class instance
     * @var self
     */
    private static $classObject;

    /**
     * A unique identifier with which log can be identified within a single request
     * @var string
     */
    private static $identifier;

    /**
     * Contains all ticket creation logs
     * @var TicketActivityLog[]
     */
    private static $ticketCreationLogs;

    /**
     * Contains all ticket updation logs
     * @var TicketActivityLog[]
     */
    private static $ticketUpdationLogs;

    /**
     * Event type
     * @var string
     */
    private $eventType;

    /**
     * Event type
     * @var string
     */
    private $actionTakerType;

    /**
     * Event type
     * @var string
     */
    private $actionTakerId;

    /**
     * @var array
     */
    private $changedValues;

    /**
     * Collection of activities
     * @var Collection
     */
    private static $logs;

    /**
     * Stores unique identifier of form fields which are attachment.
     * @internal to avoid duplicate queries in a single api call
     * @var string[]|null
     */
    private $attachmentFields = null;

    /**
     * Attributes which can be logged
     * @var array
     */
    private $loggableAttributes = ["assigned_id", 'team_id', "type_id", "department_id", "source_id", "location_id",
        "status_id", "subject", "user_id", "duedate", "priority_id", "help_topic_id", "cc_ids", "label_ids",
        "tag_ids"];

    private function __construct()
    {
        # constructor is private so that it cannot be instantiated directly from outside
        self::$identifier = self::$identifier ?? Str::uuid()->toString();
        self::$logs = new Collection();
    }

    public static function __constructStatic()
    {
        // it will created the class instance if not created already
        self::getInstance();
    }

    /**
     * Get instance of the class
     * @return TicketActivityLogRepository
     */
    public static function getInstance()
    {
        if (self::$classObject === null) {
            self::$classObject = new TicketActivityLogRepository();
        }

        return self::$classObject;
    }

    /**
     * Destroys the instance
     */
    public static function destroyInstance()
    {
        self::$classObject = null;
        self::$identifier = null;
        self::$logs = null;
    }

    /**
     * Sets ticket values
     * @param array $changedValues
     */
    public function setChangedValues(array $changedValues)
    {
        $this->changedValues = $changedValues;

        // convert ticket values from old format (database keys) to new (standard keys)
        $this->formatTicketsArrayFromOldToNewKey($this->changedValues);
    }

    /**
     * Sets event types
     * @param string $value
     * @throws \Exception
     */
    public function setActionTakerType(string $value)
    {
        if (!in_array($value, [self::USER, self::WORKFLOW, self::LISTENER, self::SLA])) {
            throw new \Exception('Invalid Action taker type');
        }

        $this->actionTakerType = $value;
    }

    /**
     * Sets event types
     * @param int $value
     * @throws \Exception
     */
    public function setActionTakerId(int $value = null)
    {
        $this->actionTakerId = $value;
    }

    /**
     * Store logs in ticket_activity_logs table
     * @param $eventType
     * @param array $changedValues
     * @param array $parentAttributes if someone wants to manipulate log attributes, logs attributes can be passed in this param
     * @throws \Exception
     */
    public function setLogs($eventType, array $changedValues, $parentAttributes = [])
    {
        if (!in_array($eventType, [
            self::TICKET_CREATED, self::TICKET_UPDATED,
            self::WORKFLOW_ENFORCED, self::LISTENER_ENFORCED, self::SLA_ENFORCED,
            self::REPLY_ADDED, self::INTERNAL_NOTE_ADDED, self::TICKET_FORWARDED, self::TICKET_FORKED
        ])) {
            throw new \Exception('Invalid Ticket event');
        }

        $this->setChangedValues($changedValues);

        $parent = $this->getParentActivity($eventType, $parentAttributes);

        foreach ($this->changedValues as $field => $value) {
            if (self::isLoggable($field)) {
                $this->injectActivityChildObject($field, $value, $parent);
            }
        }

        // check for that event in the logs
        if (!($log = $this->pullLogIfExists($eventType))) {
            $log = new Activity($eventType);
        }

        $log->data->add($parent);

        self::$logs->add($log);

        // resetting action taker Id as soon as one log is logged
        $this->actionTakerId = null;
    }

    /**
     * Pulls logs of asked type and removes
     * @param $eventType
     * @return mixed
     */
    private function pullLogIfExists($eventType)
    {
        foreach (self::$logs as $key => $logElement) {
            if ($logElement->type === $eventType) {
                self::$logs->forget($key);

                return $logElement;
            }
        }
    }

    /**
     * Saves ticket activity by eliminating activities which are invalid or duplicate
     * @param $ticketId
     */
    public static function saveActivity($ticketId)
    {
        if (!self::$logs) {
            return;
        }
        
        foreach (self::$logs as $logObject) {
            foreach ($logObject->data as $log) {
                if (!self::shallSaveParentLog($log, $logObject->type, $ticketId)) {
                    continue;
                }

                $log->ticket_id = $ticketId;

                $log->identifier = self::$identifier;

                $childrenLog = $log->children;

                // unset-ing so that while saving, it doesn't consider children as a column
                unset($log->children);

                $log = self::getLog($log);

                $sanitizedChildLogs = self::$classObject->getSanitizedChildLogs($childrenLog, $ticketId, $logObject->type);

                // when child count in 0 but parent requires children, parent should not be saved
                if ($sanitizedChildLogs->count() || self::$classObject->shouldHaveNoChild($logObject->type)) {
                    self::$classObject->slaCleanup($ticketId, $logObject->type);
                    $log->save();
                    self::$classObject->saveChildrenLogs($sanitizedChildLogs, $log, $ticketId, $logObject->type);
                }
            }
        }

        // emptying the logs once saved
        self::$logs = new Collection();
    }

    /**
     * Saves children logs by parent log
     * @param $sanitizedChildLogs
     * @param $log
     * @param $ticketId
     * @param $eventType
     */
    private function saveChildrenLogs($sanitizedChildLogs, $log, $ticketId, $eventType)
    {
        if (!self::$classObject->shouldHaveNoChild($eventType)) {
            $sanitizedChildLogs->map(function ($childLog) use ($log, $ticketId) {
                $childLog->parent_id = $log->id;
                $childLog->category = $log->category;
                $childLog->ticket_id = $ticketId;
                $childLog->identifier = self::$identifier;
                $childLog->save();
            });
        }
    }

    /**
     * Cleans up old SLA logs for the ticket in the same request
     * @param $ticketId
     * @param $eventType
     */
    private function slaCleanup($ticketId, $eventType)
    {
        // SLA old logs cleanup. Since SLA can only be enforced once, it should not be logged multiple times
        if ($eventType === self::SLA_ENFORCED) {
            // for SLA, only one SLA enforcement per request can happen
            // removing old ticket activities for SLA. One action can only have one SLA activity
            TicketActivityLog::where('ticket_id', $ticketId)->where('category', self::SLA_ENFORCED)
                ->where('identifier', self::$identifier)->get()->map(function ($element) {
                    $element->delete();
                });
        }
    }

    /**
     * Gets sanitized child logs
     * @param $childLogs
     * @param $ticketId
     * @param $eventType
     * @return Collection
     */
    private function getSanitizedChildLogs($childLogs, $ticketId, $eventType)
    {
        if (self::$classObject->shouldHaveNoChild($eventType)) {
            // returning an empty collection, since no child shuold be saved
            return new Collection();
        }

        return $childLogs->filter(function ($child) use ($eventType, $ticketId) {
            // if same key exists before with some other value and this time, it is null, it should log
            // if the very first time some value is null, it should not be logged
            $previousLogRecord = TicketActivityLog::select('value')->where("field", $child->field)->orderBy("id", "desc")->where("ticket_id", $ticketId)->first();

            if ($previousLogRecord) {
                return $previousLogRecord->value !== $child->value;
            }
            return (bool)$child->value;
        });
    }

    /**
     * Gets parent activity object
     * @param $eventType
     * @param $parentAttributes
     * @return TicketActivityLog
     */
    public function getParentActivity($eventType, $parentAttributes) : TicketActivityLog
    {
        $parent = new TicketActivityLog;
        $parent->category = $eventType;
        $parent->action_taker_type = $this->actionTakerType;
        $parent->action_taker_id = $this->getActionTakerIdForNonEnforcer();
        $parent->children = Collection::make();

        foreach ($parentAttributes as $key => $value) {
            $parent->$key = $value;
        }

        return $parent;
    }

    /**
     * Gets existing log if exists, else returns the same log object
     * @param TicketActivityLog $activityLog
     * @param string $identifier
     * @return TicketActivityLog
     */
    private static function getLog(TicketActivityLog $activityLog) : TicketActivityLog
    {
        $baseQuery = TicketActivityLog::where("identifier", self::$identifier)
            ->where("action_taker_type", $activityLog->action_taker_type)
            ->where("action_taker_id", $activityLog->action_taker_id)
            ->where("ticket_id", $activityLog->ticket_id)
            ->whereNull("parent_id");


        // if category is update and in db create OR reply activity exists, it should return that as
        if (in_array($activityLog->category, [self::TICKET_UPDATED])) {
            // check if with current category and identifier, there already is an entry, it should append to the same parent
            $existingLog = $baseQuery->whereIn("category", [self::TICKET_CREATED, self::REPLY_ADDED, self::TICKET_UPDATED])
                ->orderByRaw("FIELD(category, '".self::TICKET_CREATED."', '".self::TICKET_UPDATED."')")
                ->first();
        } else {
            // check if with current category and identifier, there already is an entry, it should append to the same parent
            $existingLog = $baseQuery->where("category", $activityLog->category)->first();
        }

        if ($existingLog) {
            return $existingLog;
        }
        return $activityLog;
    }

    /**
     * injects child activity object in parent
     * @param $field
     * @param $value
     * @param $parent
     */
    public function injectActivityChildObject($field, $value, &$parent)
    {
        $childLog = new TicketActivityLog();
        $childLog->field = $field;
        $childLog->value = $value;
        $childLog->action_taker_id = $parent->action_taker_id;
        $childLog->action_taker_type = $parent->action_taker_type;
        $parent->children->add($childLog);
    }

    /**
     * Tells if a field is loggable or not
     * @param string $field
     * @return bool
     */
    public function isLoggable(string $field)
    {
        // if field has custom_ string, it should be logged
        if (strpos($field, "custom_") !== false) {
            // attachment fields should be excluded from storing
            $this->attachmentFields = $this->attachmentFields ?? FormField::where('type', 'file')->get(['id', 'unique'])->pluck('unique')->toArray();
            return !in_array($field, $this->attachmentFields);
        }

        return in_array($field, $this->loggableAttributes);
    }

    /**
     * Gives action taker id for events other than workflow, listener and SLA
     * @return mixed|null
     */
    private function getActionTakerIdForNonEnforcer()
    {
        if ($this->actionTakerId) {
            return $this->actionTakerId;
        }

        if (Auth::check()) {
            return Auth::user()->id;
        }

        return $this->changedValues["user_id"] ?? null;
    }

    /**
     * Tells if log should be saved or not
     * @param $log
     * @param $key
     * @param $ticketId
     * @return bool
     */
    private static function shallSaveParentLog($log, $key, $ticketId)
    {
        if ($key === self::REPLY_ADDED) {
            return self::$classObject->shallLogReply($ticketId);
        }

        if ($key === self::INTERNAL_NOTE_ADDED) {
            return self::$classObject->shallLogInternalNote();
        }

        // problem => ticket update event is getting called with cc_ids. But while saving, the check to not save is removing that
        if (!$log->children->count()) {
            return self::$classObject->shouldHaveNoChild($key);
        }

        return true;
    }

    /**
     * Ticket events here are independent of tickets table and hence should have no child activity at the time of log creation
     * @param string $key
     * @return bool
     */
    private function shouldHaveNoChild(string $key)
    {
        return in_array($key, [self::REPLY_ADDED, self::INTERNAL_NOTE_ADDED, self::TICKET_FORKED, self::TICKET_FORWARDED]);
    }

    /**
     * Checks if the thread recieved should be logged or not
     * @param $ticketId
     * @return bool
     */
    private function shallLogReply($ticketId)
    {
        $isFirstThreadAlreadyGenerated = Ticket_Thread::where("ticket_id", $ticketId)->where("is_internal", 0)->count();

        $isCurrentThreadAReply = !(isset($this->changedValues["is_internal"]) && $this->changedValues["is_internal"]);

        return (bool)($isFirstThreadAlreadyGenerated && $isCurrentThreadAReply);
    }

    /**
     * Checks if the thread recieved should be logged or not
     * @return bool
     */
    private function shallLogInternalNote()
    {
        return (
            isset($this->changedValues["is_internal"]) && $this->changedValues["is_internal"] &&
            isset($this->changedValues["thread_type"]) && $this->changedValues["thread_type"] == "note"
        );
    }

    /**
     * Gets activities by ticketId
     * @param $ticketId
     * @param $limit
     * @param Closure $getLogText
     * @return
     */
    public function getByTicketId($ticketId, $limit, Closure $getLogText)
    {
        $paginatedData = TicketActivityLog::whereTicketId($ticketId)->groupBy('identifier')
            ->select('identifier')
            ->where('identifier', '!=', null)
            ->orderBy('id', 'desc')
            ->paginate($limit);

        $identifiers = $paginatedData->getCollection()->pluck('identifier')->toArray();

        $logs = TicketActivityLog::whereIn('identifier', $identifiers)->where('ticket_id', $ticketId)
            ->where("parent_id", null)
            ->with("children")
            ->orderBy("id", "asc")
            ->get();

        $paginatedData->getCollection()->transform(function ($element) use ($logs, $getLogText) {
            $log = (object)[];
            $log->id = $element->identifier;

            $logs->where('identifier', $element->identifier)->map(function ($element) use (&$log, $getLogText) {
                $log->created_at = $log->created_at ?? $element->created_at;
                $log->text = isset($log->text) ? $log->text."<hr>".$getLogText($element) : $getLogText($element);
            });
            return $log;
        });

        return $paginatedData;
    }


    /**
     * This method is used to log free text (useful for scenarios like, approval workflow, fork, merge)
     * @param $text
     * @param $ticketId
     * @param string $actionTakerType
     * @param null $actionTakerId
     * @return TicketActivityLog
     */
    public static function log($text, $ticketId, $actionTakerType = 'user', $actionTakerId = null)
    {
        return TicketActivityLog::create([
            'category'=>'general',
            'action_taker_id'=> $actionTakerId ?? Auth::user()->id ?? null,
            'action_taker_type'=> $actionTakerType,
            'ticket_id'=> $ticketId,
            'value'=> $text,
            'identifier'=> self::$identifier
        ]);
    }
}
