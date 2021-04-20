<?php

namespace App\Http\Controllers\Common;

use App\Exceptions\DependencyNotFoundException;
use App\Http\Controllers\Common\Dependency\DependencyDetails;
use App\Model\Common\TicketActivityLog;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Form\FormField;
use App\Repositories\TicketActivityLogRepository;
use App\Traits\EnforcerHelper;
use App\User;
use Carbon\Carbon;
use Closure;
use Config;
use Illuminate\Http\Request;
use Lang;

/**
 * Handles all ticket activity log related operations
 * @author  avinash kumar <avinash.kumar@ladybirdeb.com>
 */
class TicketActivityLogController extends Controller
{
    use EnforcerHelper;

    private $agentTimezone;

    private $ticketActivityLogRepository;

    public function __construct()
    {
        $this->middleware("role.agent");
        $this->ticketActivityLogRepository = TicketActivityLogRepository::getInstance();
    }

    public function getTicketActivity($ticketId, Request $request)
    {
        $this->agentTimezone = agentTimeZone();

        $activities = $this->ticketActivityLogRepository
            ->getByTicketId($ticketId, $request->input('limit', 10), Closure::fromCallable([$this, 'getTextByCategory']));

        return successResponse("", $activities);
    }


    /**
     * Gets text by its category
     * @param TicketActivityLog $activityLog
     * @return mixed|string
     */
    private function getTextByCategory(TicketActivityLog $activityLog)
    {
        switch ($activityLog->category) {
            case $this->ticketActivityLogRepository::TICKET_CREATED:
                return $this->getTicketCreatedText($activityLog);

            case $this->ticketActivityLogRepository::TICKET_UPDATED:
                return $this->getTicketUpdatedText($activityLog);

            case $this->ticketActivityLogRepository::SLA_ENFORCED:
                return $this->getEnforcerLogText($activityLog, 'SLA');

            case $this->ticketActivityLogRepository::WORKFLOW_ENFORCED:
                return $this->getEnforcerLogText($activityLog, "Workflow");

            case $this->ticketActivityLogRepository::LISTENER_ENFORCED:
                return $this->getEnforcerLogText($activityLog, "Listener");

            case $this->ticketActivityLogRepository::REPLY_ADDED:
                return $this->getReplyAddedText($activityLog);

            case $this->ticketActivityLogRepository::INTERNAL_NOTE_ADDED:
                return $this->getInternalNoteAddedText($activityLog);

            case $this->ticketActivityLogRepository::TICKET_FORWARDED:
                return $this->getTicketForwardedText($activityLog);

            default:
                return $activityLog->value;
        }
    }

    /**
     * Gets name of the enforcer
     * @param $enforcerType
     * @param $enforcerId
     * @return array|string|null
     */
    private function getEnforcerText($enforcerType, $enforcerId)
    {
        $enforcer = $this->getParentModel($enforcerType)::where("id", $enforcerId)->select("id", "name")->first();

        if (!$enforcer) {
            return $this->getStyledTextForDeleted();
        }

        return $enforcer->name;
    }

    /**
     * Gets text for ticket created activity log
     * @param TicketActivityLog $activityLog
     * @return string
     */
    private function getTicketCreatedText(TicketActivityLog $activityLog) : string
    {
        $userWithHyperlink = $this->getActionTaker($activityLog);

        $baseText = Lang::get("lang.ticket_has_been_created_by_user_with", ["user" => $userWithHyperlink]);

        $children = $activityLog->children;

        $childrenTextArray = [];

        foreach ($children as $child) {
            $childrenTextArray[] = Lang::get(
                "lang.field_as_value",
                ["field"=> $this->getFieldWithKey($child->field), "value"=> $this->getValueWithKeyAndId($child->field, $child->value)]
            );
        }

        $baseText = $baseText." ". implode(", ", $childrenTextArray);

        return $baseText;
    }

    /**
     * Gets text for ticket created activity log
     * @param TicketActivityLog $activityLog
     * @return string
     */
    private function getTicketUpdatedText(TicketActivityLog $activityLog) : string
    {
        $userWithHyperlink = $this->getActionTaker($activityLog);

        $baseText = Lang::get("lang.ticket_has_been_updated_by_user", ["user" => $userWithHyperlink]);

        $children = $activityLog->children;

        $childrenTextArray = [];

        foreach ($children as $child) {
            $childrenTextArray[] = Lang::get(
                "lang.field_as_value",
                ["field"=> $this->getFieldWithKey($child->field), "value"=> $this->getValueWithKeyAndId($child->field, $child->value)]
            );
        }

        $baseText = $baseText.". ".Lang::get("lang.set")." ". implode(", ", $childrenTextArray);

        return $baseText;
    }

    /**
     * Gets text for ticket created activity log
     * @param TicketActivityLog $activityLog
     * @param $enforcerType
     * @return string
     */
    private function getEnforcerLogText(TicketActivityLog $activityLog, $enforcerType) : string
    {
        $enforcerText = $this->getActionTaker($activityLog);

        $baseText = Lang::get("lang.enforcer_has_been_enforced_on_ticket", ["enforcerName" => $this->getStyledValue($enforcerText), "enforcerType"=> $this->getStyledKey($enforcerType)]);

        $children = $activityLog->children;

        $childrenTextArray = [];

        foreach ($children as $child) {
            $childrenTextArray[] = Lang::get(
                "lang.field_as_value",
                ["field"=> $this->getFieldWithKey($child->field), "value"=> $this->getValueWithKeyAndId($child->field, $child->value)]
            );
        }

        $baseText = $baseText.". ".Lang::get("lang.set")." ". implode(", ", $childrenTextArray);

        return $baseText;
    }

    /**
     * Gets text for ticket created activity log
     * @param TicketActivityLog $activityLog
     * @return string
     */
    private function getReplyAddedText(TicketActivityLog $activityLog) : string
    {
        $userWithHyperlink = $this->getActionTaker($activityLog);

        $baseText = Lang::get("lang.reply_has_been_made_by", ["user" => $userWithHyperlink]);

        $children = $activityLog->children;

        $childrenTextArray = [];

        foreach ($children as $child) {
            $childrenTextArray[] = Lang::get(
                "lang.field_as_value",
                ["field"=> $this->getFieldWithKey($child->field), "value"=> $this->getValueWithKeyAndId($child->field, $child->value)]
            );
        }

        if (count($childrenTextArray)) {
            $baseText = $baseText.". ".Lang::get("lang.set")." ". implode(", ", $childrenTextArray);
        }

        return $baseText;
    }

    /**
     * Gets text for ticket created activity log
     * @param TicketActivityLog $activityLog
     * @return string
     */
    private function getInternalNoteAddedText(TicketActivityLog $activityLog) : string
    {
        $userWithHyperlink = $this->getActionTaker($activityLog);

        $baseText = Lang::get("lang.internal_note_has_been_added_by", ["user" => $userWithHyperlink]);

        return $baseText;
    }

    /**
     * Gets text for ticket created activity log
     * @param TicketActivityLog $activityLog
     * @return string
     */
    private function getTicketForwardedText(TicketActivityLog $activityLog) : string
    {
        $userWithHyperlink = $this->getActionTaker($activityLog);

        $baseText = Lang::get("lang.ticket_has_been_forwarded_to_by", ["user" => $userWithHyperlink, "to"=> $this->getStyledValue(implode(", ", $activityLog->value))]);

        return $baseText;
    }

    /**
     * Gets name of the field in current language
     * @param string $key
     * @return string
     */
    private function getFieldWithKey(string $key) : string
    {
        $keyFieldPair = [
            "assigned_id" => Lang::get("lang.assigned_agent"),
            "type_id" => Lang::get("lang.type"),
            "department_id" => Lang::get("lang.department"),
            "help_topic_id" => Lang::get("lang.helptopic"),
            "source_id" => Lang::get("lang.source"),
            "location_id" => Lang::get("lang.location"),
            "status_id" => Lang::get("lang.status"),
            "subject" => Lang::get("lang.subject"),
            "user_id" => Lang::get("lang.owner"),
            "duedate" => Lang::get("lang.due_date"),
            "priority_id" => Lang::get("lang.priority"),
            "cc_ids" => Lang::get("lang.collaborators"),
            "label_ids" => Lang::get("lang.labels"),
            "tag_ids" => Lang::get("lang.tags"),
            "team_id" => Lang::get("lang.assigned_team"),
        ];

        if (strpos($key, "custom_") !== false) {
            $formFieldId = str_replace("custom_", "", $key);
            $customField = FormField::whereId($formFieldId)->select("id")->first();
            if (!$customField) {
                return $this->getStyledTextForDeleted(Lang::get("lang.deleted_custom_field"));
            }
            return $this->getStyledKey($customField->label);
        }

        if (!isset($keyFieldPair[$key])) {
            throw new \UnexpectedValueException("invalid key for ticket activity. 
            Make sure that key is defined in TicketActivityLog's loggableAttributes property");
        }

        return $this->getStyledKey($keyFieldPair[$key]);
    }

    /**
     * Gets value of the dependency in formatted form
     * @param string $key
     * @param $value
     * @return string
     */
    private function getValueWithKeyAndId(string $key, $value)
    {
        /**
         * when value is empty OR null, it means value has been made none
         * but when a foriegn key is present but the owner of that key is not, that means that owner has been delete
         */
        if (!$value) {
            return "<i style='color:red;'>None</i>";
        }

        try {
            switch ($key) {
                case "subject":
                    return $value;

                case "duedate":
                    return $this->getStyledValue(Carbon::parse($value)->setTimezone($this->agentTimezone)->format(dateTimeFormat()));

                case strpos($key, "custom_") !== false:
                    return $this->getCustomFieldValue($key, $value);

                default:
                    return $this->getStyledValue((new DependencyDetails())->getDependencyDetails($key, $value, false, true));
            }
        } catch (DependencyNotFoundException $e) {
            return $this->getStyledTextForDeleted();
        }
    }

    /**
     * Gets action taker of the activity
     * @param TicketActivityLog $activityLog
     * @return array|string|null
     */
    private function getActionTaker(TicketActivityLog $activityLog)
    {
        if (!$activityLog->action_taker_id) {
            return Lang::get("lang.system");
        }

        switch ($activityLog->action_taker_type) {
            case "workflow":
            case "listener":
            case "sla":
                return $this->getEnforcerText($activityLog->action_taker_type, $activityLog->action_taker_id);

            case "user":
                return $this->getUserText($activityLog->action_taker_id);

            default:
                throw new \UnexpectedValueException("invalid action taker");
        }
    }

    /**
     * Gets user hyperlink text
     * @param $actionTakerId
     * @return array|string|null
     */
    private function getUserText($actionTakerId)
    {
        $creator = User::whereId($actionTakerId)
            ->select("id", "email", "first_name", "last_name", "user_name")
            ->first();

        if (!$creator) {
            // deleted user
            return $this->getStyledTextForDeleted();
        }

        $url = Config::get("app.url")."/user/$creator->id";

        return "<a href=$url>$creator->full_name</a>";
    }

    /**
     * Gets styles keys
     * @param $key
     * @return string
     */
    private function getStyledKey($key)
    {
        return "<strong>$key</strong>";
    }

    /**
     * Gets styles values
     * @param $value
     * @return string
     */
    private function getStyledValue($value)
    {
        // is_countable will work for both array and collection
        if (is_countable($value)) {
            $formattedValue = [];
            foreach ($value as $item) {
                $formattedValue[] = $item->name ?? $item->full_name ?? $item;
            }
            $value = implode(", ", $formattedValue);
        }

        if (!$value) {
            return $this->getStyledTextForDeleted();
        }

        return "<i>$value</i>";
    }

    /**
     * Gets styled text for deleted entries
     * @param string $text
     * @return string
     */
    private function getStyledTextForDeleted($text = "")
    {
        $text = $text ?: trans('lang.deleted');

        return "<i style='color:red;'>$text</i>";
    }

    /**
     * @param string $key
     * @param string|array $value
     * @return string
     */
    private function getCustomFieldValue(string $key, $value)
    {
        $formFieldId = str_replace("custom_", "", $key);

        if (FormField::whereId($formFieldId)->value("type") === 'date') {
            $value = changeTimezoneForDatetime($value, 'UTC', $this->agentTimezone)->format(dateTimeFormat());
        }

        return $this->getStyledValue($value);
    }
}
