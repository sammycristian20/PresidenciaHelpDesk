<?php

namespace App\Console\Commands;

use App\Console\LoggableCommand;
use App\User;
use App\Model\helpdesk\Email\Emails;
use App\Model\helpdesk\Settings\Email as SystemMail;
use App\Model\helpdesk\Settings\Plugin;

class TaskNotification extends LoggableCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $task;
    protected $signature = 'task:alert {alert_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This function handles task alerts';

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleAndLog()
    {
        
        $type = function ($query) {
           $query->where('repeat_alerts', $this->argument('alert_type'));
        };
        $tasks = $this->task->with('alerts')->whereHas('alerts', $type)->get();
        $data = Emails::find(SystemMail::pluck('sys_email')->first());
        $mail = new \App\Http\Controllers\Common\PhpMailController;
        $mail->setMailConfig($data);
        //dd($tasks);
        foreach ($tasks as $key => $value) {
            if($value->assignedTo->isEmpty()){
                dd("fine");
                \Mail::send('emails.alert', ['task' => $value], function ($m) use ($value) {
                    $m->from('hello@app.com', 'Faveo');
                    $m->to(User::where('id', (int)$value->created_by)->value('email'), User::where('id', (int)$value->created_by)->value('email'))->subject('Task Alert');
                });
            }
            else{
                foreach ($value->assignedTo as $key => $assigned) {
                    \Mail::send('emails.alert', ['task' => $value], function ($m) use ($assigned) {
                        $m->from('hello@app.com', 'Faveo');
                        $m->to(User::where('id', (int)$assigned->user_id)->value('email'), User::where('id', (int)$assigned->user_id)->value('email'))->subject('Task Alert');
                    });

                }
            }
            
        }
        \Log::info("Task Alert mail sent");
    }
}
