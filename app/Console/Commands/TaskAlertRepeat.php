<?php

namespace App\Console\Commands;

use App\Console\LoggableCommand;
use App\User;
use \Carbon\Carbon;
use App\Model\helpdesk\Settings\Alert;
use App\Model\helpdesk\Settings\Plugin;
use App\Jobs\Notifications as NotifyQueue;

class TaskAlertRepeat extends LoggableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:repeat {type}';
    protected $task, $notify, $user, $alert;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'notifies task alert';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {   
        parent::__construct();
        
        // if faveo is not installed or calender plugin is not installed, it should not run this 
        if(!isInstall() || !Plugin::where('name', 'Calendar')->first()){
            return;
        }

        $this->task = new \App\Plugins\Calendar\Model\Task;
        $this->user = new User;
        $this->alert = new Alert;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleAndLog()
    {
            $data = $this->argument('type');
            switch ($data) {
                case 'daily':
                    $tasks = $this->task->with('assignedTo')->whereHas('alerts', function($q){
                        $q->where('repeat_alerts', 'daily');
                    })->where('status', 'active')
                        ->where('task_end_date', '>', \Carbon\Carbon::now())
                        ->get();
                    \Log::info($tasks);
                    foreach ($tasks as $key => $value) {
                        if($value->task_end_date > \Carbon\Carbon::now()){
                            \Log::info("executing daily remonder");
                            $this->sendReminder($value);
                        }
                    }
                break;
                case 'weekly':
                    $tasks = $this->task->whereHas('alerts', function($q){
                        $q->where('repeat_alerts', 'weekly');
                    })->where('status', 'active')
                        ->where('task_end_date', '>', \Carbon\Carbon::now())
                        ->get();

                    foreach ($tasks as $key => $value) {
                        if($value->task_end_date > \Carbon\Carbon::now()){
                            \Log::info("executing daily remonder");
                            $this->sendReminder($value);  
                        }
                    }


                case 'monthly':
                    $tasks = $this->task->whereHas('alerts', function($q){
                        $q->where('repeat_alerts', 'weekly');
                    })->where('status', 'active')
                        ->where('task_end_date', '>', \Carbon\Carbon::now())
                        ->get();


                    foreach ($tasks as $key => $value) {
                        if($value->task_end_date > \Carbon\Carbon::now()){
                            \Log::info("executing daily remonder");
                            $this->sendReminder($value);                           
                        }
                    }
                break;

                case 'reminder':
                    $tasks = $this->task->with('assignedTo')->where('task_start_date', '>', Carbon::now())->get();
                    if($this->checkEnabled('task-reminder-alert')){
                        foreach ($tasks as $key => $task) {
                            switch($task->task_start_date->diffInMinutes(\Carbon\Carbon::now())){
                                case 5:
                                    $this->sendReminder($task);
                                    break;
                                case 15:
                                    $this->sendReminder($task);
                                    break;
                                case 30:
                                    $this->sendReminder($task);
                                    break;
                                case 60:
                                    $this->sendReminder($task);
                                    break;
                                case 120:
                                    $this->sendReminder($task);
                                    break;
                                case 1440:
                                    $this->sendReminder($task);
                                    break;

                            }
                        }
                    }
                break;
            }
            // $tasks = $this->task->where('status', 'active');

            
    }

    private function checkEnabled($type){
        if(Alert::where('key', $type)->value('value') == 1)
            return true;
        return false;
    }


   private function sendReminder($task){
    \Log::info("dsdasds");
      $data =  [
         'to' => $task->created_by,
         'by' => $task->created_by,
         'table' => "tasks",
         'row_id' => $task->id,
         'url' => url('task/'.$task->id.'/edit'),
         'message' => "Your task ".$task->task_name." is due on ".$task->task_end_date->format('D d-M-Y')
      ];

      if($this->alert->isValueExists('task-reminder-alert-person','creator')){
         \Log::info("Executing task reminder");
         if($this->alert->isValueExists('task-reminder-alert-mode','email')){
            $template_variables = array('receiver_name' => $this->user->where('id', $task->created_by)->value('user_name'), 'task_name' => $task->task_name, 'task_end_date' => $task->task_end_date);
            NotifyQueue::dispatch("email-notification", $template_variables, 'task', 'task-reminder', [],[$this->user->where('id', $task->created_by)->value('email')]);
         }
         if($this->alert->isValueExists('task-reminder-alert-mode','in-app-notify')){
            NotifyQueue::dispatch("in-app-notification", $data, 'task');
         }
      }
      if($this->alert->isValueExists('task-reminder-alert-person','assignee')){
         if(count($task->assignedTo) > 0){
            foreach ($task->assignedTo as $key => $value){
               if($value->user_id != $task->created_by){
                  if($this->alert->isValueExists('task-reminder-alert-mode','email')){
                     $template_variables = array('receiver_name' => $this->user->where('id', $value->user_id)->value('user_name'), 'task_name' => $task->task_name, 'task_end_date' => $task->task_end_date);
                     NotifyQueue::dispatch('email-notification', $template_variables, 'task', 'task-reminder', [], [$this->user->where('id', $value->user_id)->value('email')]);
                  }
               }
               if($this->alert->isValueExists('task-reminder-alert-mode','in-app-notify')){
                  $data['to'] = $value->user_id;
                  NotifyQueue::dispatch("in-app-notification", $data, 'task');
               }
            } 
         }
      }
   }
}
