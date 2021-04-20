<?php

namespace App\Plugins\Calendar\Handler;

use App\Http\Controllers\Common\NotificationController;
use App\Http\Controllers\Common\TemplateVariablesController;
use App\Model\Common\Template;
use App\Model\helpdesk\Notification\PushNotification;
use App\Model\helpdesk\Settings\Alert;
use App\Model\helpdesk\Settings\System;
use App\Plugins\Calendar\Model\Task;
use App\Plugins\Calendar\Model\TaskMail;
use App\User;
use Carbon\Carbon;

class TaskNotificationHandler
{
    private $taskMappings = [
        'task-created' => [
            'alert' => 'new-task-alert',
            'mode' => 'new-task-alert-mode',
            'person' => 'new-task-alert-person'
        ],
        'task-reminder' => [
            'alert' => 'task-reminder-alert',
            'mode' => 'task-reminder-alert-mode',
            'person' => 'task-reminder-alert-person'
        ],
        'task-update' => [
            'alert' => 'task-update-alert',
            'mode' => 'task-update-alert-mode',
            'person' => 'task-update-alert-person'
        ],
        'task-status' => [
            'alert' => 'task-status-alert',
            'mode' => 'task-status-alert-mode',
            'person' => 'task-status-alert-person'
        ],
        'task-assigned' => [
            'alert' => 'task-assign-alert',
            'mode' => 'task-assign-alert-mode',
            'person' => 'task-assign-alert-person'
        ]

    ];

    private $task;

    private $event;

    private $user;

    private $dateTimeFormat;

    private $agents;

    public function __construct(Task $task, $event, $user, $agents = [])
    {
        $this->task = $task;
        $this->event = $event;
        $this->user = $user;
        $this->agents = $agents;
        $dateTimeFormat = System::value('date_time_format');
        if ($dateTimeFormat == 'human-read') {
            $this->dateTimeFormat = 'd-m-Y h:i a';
        } else {
            $this->dateTimeFormat = $dateTimeFormat;
        }
    }

    public function handle()
    {
        $alerts = [];
        $alerts['alert'] = Alert::where('key', $this->taskMappings[$this->event]['alert'])->value('value');
        $alerts['mode'] = Alert::where('key', $this->taskMappings[$this->event]['mode'])->value('value');
        $alerts['person'] = Alert::where('key', $this->taskMappings[$this->event]['person'])->value('value');

        if ($alerts['alert'] && $alerts['person'] && $alerts['mode']) {
            $recipients = explode(',', $alerts['person']);
            $mediums = explode(',', $alerts['mode']);
            $this->notify($recipients, $mediums);
        }
    }

    private function sendTaskMail($recipient)
    {
        $taskCreatorDetails = User::where('id', $this->task->created_by->id)
            ->first(['user_name','email','id','role','agent_tzone']);

        $agents = ($this->agents) ?: $this->task->assignedTo->pluck('user_id')->toArray();

        $agentDetails = User::whereIn('id', $agents)
            ->get(['user_name','email', 'id'])->toArray();

        $agentUsernames = array_column($agentDetails,'full_name');


        $templateVariable = [
            'task_name' => $this->task->task_name,
            'agent_name' => ($agentUsernames) ? implode(',', $agentUsernames) : 'None',
            'created_by' => $taskCreatorDetails->full_name,
            'status' => $this->task->status,
            'updated_by' => ($this->user)
                ? User::where('id', $this->user->id)->first(['user_name'])->toArray()['full_name'] : null
        ];

        if ($recipient === 'creator') {
            $templateVariable['receiver_name'] = $taskCreatorDetails->full_name;
            $templateVariable['task_end_date'] = $this->getDateTimeInAgentTimeZoneForSendingNotifications(
                $this->task->task_end_date, $taskCreatorDetails
            );
            $this->dispatchMail($templateVariable, 'owner', $taskCreatorDetails->email);

        } elseif ($recipient === 'assignee') {
            foreach ($agentDetails as $agentDetail) {

                if ($this->isThisPersonCanBeSkippedForNotification($agentDetail['id'])) {
                    continue;
                }

                $templateVariable['receiver_name'] = $agentDetail['full_name'];
                $templateVariable['task_end_date'] = $this->getDateTimeInAgentTimeZoneForSendingNotifications(
                    $this->task->task_end_date, User::find($agentDetail['id'])
                );
                $this->dispatchMail($templateVariable, 'assignee', $agentDetail['email']);
            }
        }

    }

    private function dispatchMail($templateVariable, $recipient, $recipientAddress)
    {
        // if event is 'task-created' & recipient is 'owner' template becomes 'task-created-owner
        $templateName = $this->event."-".$recipient;

        $template = Template::where('name', $templateName)->first(['message','subject']);

        if ($template) {

            $templateVariableValues = (new TemplateVariablesController)->getVariableValues($templateVariable);

            $message = $template->message;

            foreach ($templateVariableValues as $k => $v) {
                $replaced = str_replace($k, $v, $message);
                $message = $replaced;
            }

            $mailBody = $message;

            TaskMail::create([
                 'to' => $recipientAddress,
                 'content' => $mailBody,
                 'subject' => $template->subject
             ]);
        }

    }

    private function notify(array $recipients, array $mediums)
    {
        if ($this->task->is_private) {
            foreach (array_keys($recipients, 'assignee', true) as $key) {
                unset($recipients[$key]); //removing, when task is private it should not send to assignees
            }
        }

        foreach ($recipients as $recipient) {
            if (in_array('email', $mediums)) {
                $this->sendTaskMail($recipient);
            }
        }

        if (in_array('in-app-notify', $mediums)) {
            $this->sendTaskPushNotification($recipients);
        }
    }

    private function sendTaskPushNotification($recipients)
    {
        $pushDataRecipients = [];

         foreach ($recipients as $recipient) {
             $notificationData = $this->getNotificationData($recipient);

             (new NotificationController)->createNotification($notificationData);

             $pushDataRecipients = explode(',', $notificationData['to']);

             foreach ($pushDataRecipients as $pushDataRecipient) {
                 $pushDataInsertArray[] = [
                     'message' => "System ". $notificationData['message'],
                     'url' => $notificationData['url'],
                     'subscribed_user_id' => $pushDataRecipient,
                     'status' => 'pending'
                 ];
             }

             PushNotification::insert($pushDataInsertArray);
         }
    }

    /**
     * Returns the push notification data
     * @param $recipient
     * @return array
     */
    private function getNotificationData($recipient)
    {
        $recipients = [];

        $event = $this->taskMappings[$this->event]['alert'];
        $eventMessageStringInLangFile = $event."-message-".$recipient;

        $agents = ($this->agents) ?: $this->task->assignedTo->pluck('user_id')->toArray();

        $agentDetails = User::whereIn('id', $agents)
            ->get(['user_name'])->toArray();

        $agentUsernames = array_column($agentDetails,'user_name');

        $parametersForLangFile = [
            'name' => $this->task->task_name,
            'date' => $this->getDateTimeInAgentTimeZoneForSendingNotifications(
                $this->task->task_end_date, User::find($this->task->created_by->id)
            ),
            'status' => $this->task->status,
            'by' => ($this->user) ? $this->user->name() : null,
            'agents' => implode(',', $agentUsernames)
        ];

        if ($recipient === 'creator') {
            $notificationRecipients = $this->task->created_by->id;
        } else {
            foreach ($agents as $agent) {
                if ($this->isThisPersonCanBeSkippedForNotification($agent)) {
                    continue;
                }
                $recipients[] = $agent;
            }
            $notificationRecipients = implode(',', $recipients);
        }

        return  [
            'message' => trans("Calendar::lang.$eventMessageStringInLangFile", $parametersForLangFile),
            'table' => "tasks",
            'by' => 'system',
            'row_id' => $this->task->id,
            'url' => url('/tasks/task/' . $this->task->id),
            'to' => $notificationRecipients
        ];
    }

    private function isThisPersonCanBeSkippedForNotification($personId)
    {
        $skip = false;
        if (
            in_array($this->event, array_keys($this->taskMappings))
            && $this->task->created_by->id === $personId
        ) {
            $skip = true;
        }
        return $skip;
    }

    /**
     * Gets agent timezone from UTC for notifications
     * @param $date
     * @param User $user
     * @return string
     */
    private function getDateTimeInAgentTimeZoneForSendingNotifications($date, User $user)
    {
        return Carbon::parse($date)
            ->setTimeZone(agentTimeZone($user))->format($this->dateTimeFormat);
    }
}
