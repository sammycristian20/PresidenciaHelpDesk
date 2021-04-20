<?php

namespace App\Plugins\Calendar\database\seeds\v_1_0_2;

use App\Model\Common\Template;
use App\Model\Common\TemplateType;
use App\Model\Common\TemplateSet;
use App\Model\MailJob\Condition;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private $insertArray = [];

    public function run()
    {
        $this->updateTemplateNames();
        $this->addNewTemplateEntries();
        $this->cronConditionSeeder();
    }

    private function cronConditionSeeder()
    {
        $calendarPluginEnabled =  Condition::where('job', 'calendar')->value('value');
        if (!$calendarPluginEnabled) {
            $arrayForSeeding = $this->getSeedArrayForTaskReminderJob();
            Condition::updateOrCreate(["job" => "task-cron-reminder"], $arrayForSeeding);
        }
    }

    private function getSeedArrayForTaskReminderJob()
    {
        return [
            "job" => "task-cron-reminder", "value" => "daily",
            "icon" => "fa fa-tasks", "command" => "task:remind",
            "job_info" => "task-cron-reminder-info", "plugin_job" => 1,
            "plugin_name" => "Calendar",
            'active' => 1,
        ];
    }


    private function updateTemplateNames()
    {
        TemplateType::where('name', 'task-created')->update(['name' => 'task-created-owner']);
        TemplateType::where('name', 'task-reminder')->update(['name' => 'task-reminder-owner']);
        TemplateType::where('name', 'task-update')->update(['name' => 'task-update-owner']);
        TemplateType::where('name', 'task-status')->update(['name' => 'task-status-owner']);
        TemplateType::where('name', 'task-assigned')->update(['name' => 'task-assigned-assignee']);
        TemplateType::where('name', 'task-deleted')->update(['name' => 'task-deleted-owner']);

        Template::where('name', 'task-reminder')->update(['name' => 'task-reminder-owner', 'subject' => 'Task Reminder']);

        Template::where('name', 'task-update')->update(['name' => 'task-update-owner', 'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
            .'Your task {!! $task_name !!} has been updated by {!! $updated_by !!} , and it is due on {!! $task_end_date !!}.</p>'
            .'<br />'
            .'Kind Regards,<br />'
            .'{!! $system_from !!}'
       ]);

        Template::where('name', 'task-created')->update(['name' => 'task-created-owner','subject' => 'Task Created', 'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
            .'Your task {!! $task_name !!} has been created and it is due on {!! $task_end_date !!}. </p>'
            .'<br />'
            .'Kind Regards,<br />'
            .'{!! $system_from !!}'
        ]);

        Template::where('name', 'task-status')->update(['name' => 'task-status-owner','message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                .'Status of your task {!! $task_name !!} has been changed to {!! $status !!}.</p>'
                .'<br />'
                .'Kind Regards,<br />'
                .'{!! $system_from !!}'
       ]);

        Template::where('name', 'task-assigned')->update(['name' => 'task-assigned-assignee','message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
            .'You have been assigned to task {!! $task_name !!} by {!! $created_by !!}'
            .' and it is due on {!! $task_end_date !!}.</p>'
            .'<br />'
            .'Kind Regards,<br />'
            .'{!! $system_from !!}'
         ]);

        Template::where('name', 'task-deleted')->update(['name' => 'task-deleted-owner',]);
        Template::where('name', 'task-assigned-owner')->update(['message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
            .'Your task {!! $task_name !!} has been assigned to {!! $agent_name !!} and'
            .' it is due on {!! $task_end_date !!}.</p>'
            .'<br />'
            .'Kind Regards,<br />'
            .'{!! $system_from !!}'
       ]);
    }

    private function addNewTemplateEntries()
    {
        foreach (TemplateSet::pluck('id')->toArray() as $setId) {
            $this->addTaskCreatedTemplateForAssignee($setId);
            $this->addTaskReminderTemplateForAssignee($setId);
            $this->addTaskUpdateTemplateForAssignee($setId);
            $this->addTaskDeletedTemplateForAssignee($setId);
            $this->addTaskStatusTemplateForAssignee($setId);
        }
    }

    private function addTaskCreatedTemplateForAssignee($setId)
    {
        $type = TemplateType::updateOrCreate(['name' => 'task-created-assignee'], ['name' => 'task-created-assignee']);
        Template::updateOrCreate(['name' => 'task-created-assignee', 'set_id' => $setId], [
            'variable' => '1',
            'subject' => 'Task created',
            'name' => 'task-created-assignee',
            'type' => $type->id,
            'set_id' => $setId,
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                . 'New task {!! $task_name !!} has been created, and it is due on {!! $task_end_date !!}. </p>'
                .'<br />'
                . 'Kind Regards,<br />'
                . '{!! $system_from !!}',
            'template_category' => 'common-templates'
        ]);
    }

    private function addTaskReminderTemplateForAssignee($setId)
    {
        $type = TemplateType::updateOrCreate(['name' => 'task-reminder-assignee'], ['name' => 'task-reminder-assignee']);
        Template::updateOrCreate(['name' => 'task-reminder-assignee', 'set_id' => $setId], [
            'variable' => '1',
            'subject' => 'Task Reminder',
            'name' => 'task-reminder-assignee',
            'type' => $type->id,
            'set_id' => $setId,
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                . 'Your assigned task {!! $task_name !!} is due on {!! $task_end_date !!}. </p>'
                .'<br />'
                . 'Kind Regards,<br />'
                . '{!! $system_from !!}',
            'template_category' => 'common-templates'
        ]);
    }

    private function addTaskUpdateTemplateForAssignee($setId)
    {
        $type = TemplateType::updateOrCreate(['name' => 'task-update-assignee'], ['name' => 'task-update-assignee']);
        Template::updateOrCreate(['name' => 'task-update-assignee', 'set_id' => $setId], [
            'variable' => '1',
            'subject' => 'Task Update',
            'name' => 'task-update-assignee',
            'type' => $type->id,
            'set_id' => $setId,
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                . 'Your assigned task {!! $task_name !!} has been updated by {!! $updated_by !!}, and is due on {!! $task_end_date !!}.</p>'
                .'<br />'
                . 'Kind Regards,<br />'
                . '{!! $system_from !!}',
            'template_category' => 'common-templates'
        ]);
    }

    private function addTaskStatusTemplateForAssignee($setId)
    {
        $type = TemplateType::updateOrCreate(['name' => 'task-status-assignee'], ['name' => 'task-status-assignee']);
        Template::updateOrCreate(['name' => 'task-status-assignee', 'set_id' => $setId], [
            'variable' => '1',
            'subject' => 'Task status update',
            'name' => 'task-status-assignee',
            'type' => $type->id,
            'set_id' => $setId,
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                . 'Status of your assigned task {!! $task_name !!} has been changed to {!! $status !!}.</p>'
                .'<br />'
                . 'Kind Regards,<br />'
                . '{!! $system_from !!}',
            'template_category' => 'common-templates'
        ]);
    }

    private function addTaskDeletedTemplateForAssignee($setId)
    {
        $type = TemplateType::updateOrCreate(['name' => 'task-deleted-assignee'], ['name' => 'task-deleted-assignee']);
        Template::updateOrCreate(['name' => 'task-deleted-assignee', 'set_id' => $setId], [
            'variable' => '1',
            'subject' => 'Task Deleted',
            'name' => 'task-deleted-assignee',
            'type' => $type->id,
            'set_id' => $setId,
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                . 'Your assigned task {!! $task_name !!} was deleted by {!! $updated_by !!}.</p>'
                . 'Kind Regards,<br />'
                . '{!! $system_from !!}',
            'template_category' => 'common-templates'
        ]);
    }
}
