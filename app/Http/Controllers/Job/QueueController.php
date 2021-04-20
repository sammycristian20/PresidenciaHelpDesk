<?php

namespace App\Http\Controllers\Job;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\helpdesk\PHPController as Controller;
use App\Model\MailJob\QueueService;
use App\Model\MailJob\FaveoQueue;
use App\Model\MailJob\Condition;
use Exception;
use Form;
use App\Http\Requests\helpdesk\Queue\QueueRequest;

class QueueController extends Controller {
    
    public function __construct() {
        $this->middleware('roles')->only('index','edit','update','activate');
    }

    public function index() {
        try {
            $cronPath = base_path('artisan');
            $queue = new QueueService();
            $queues = $queue->select('id', 'name', 'status')->get();
            $paths = $this->getPHPBinPath();
            return view('themes.default1.admin.helpdesk.queue.index', compact('queues', 'paths', 'cronPath'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function edit($id) {
        try {
            $queues = new QueueService();
            $queue = $queues->find($id);
            if (!$queue) {
                throw new Exception("Sorry we can not find your request");
            }
            return view('themes.default1.admin.helpdesk.queue.edit', compact('queue'));
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function update($id, QueueRequest $request) {
        
        try {
            $values = $request->except('_token');
            $queues = new QueueService();
            $queue = $queues->find($id);
            if (!$queue) {
                throw new Exception("Sorry we can not find your request");
            }
            $setting = new FaveoQueue();
            $settings = $setting->where('service_id', $id)->get();
            if ($settings->count() > 0) {
                foreach ($settings as $set) {
                    $set->delete();
                }
            }
            if (count($values) > 0) {
                foreach ($values as $key => $value) {
                    $setting->create([
                        'service_id' => $id,
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Updated');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function activate(QueueService $queue) {
        try {
            $activeQueue = QueueService::where('status', 1)->first();

            if ($queue->isActivate()==false&&$queue->id!=1&&$queue->id!=2) {
                throw new Exception("To activate $queue->name , Please configure it first");
            }
            if ($activeQueue) {
                $activeQueue->status = 0;
                $activeQueue->save();
            }
            $queue->status = 1;
            $queue->save();
            $this->updateSnapShotJob($queue);
            $result=$queue->name." ". 'Activated successfully';  
            
            return redirect()->back()->with('success', $result);
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
    
    public function getForm(Request $request){
        $queueid = $request->input('queueid');
        $form = $this->getFormById($queueid);
        return $form;
    }

    public function getShortNameById($queueid) {
        $short = "";
        $queues = new QueueService();
        $queue = $queues->find($queueid);
        if ($queue) {
            $short = $queue->short_name;
        }
        return $short;
    }

    public function getIdByShortName($short) {
        $id = "";
        $queues = new QueueService();
        $queue = $queues->where('short_name', $short)->first();
        if ($queue) {
            $id = $queue->id;
        }
        return $id;
    }

    public function getFormById($id) {
        try {
        $short = $this->getShortNameById($id);
        $form = "";
        switch ($short) {
            case "beanstalkd":
                $form .= "<div class='row'>";
                $form .= $this->form($short, 'Driver', 'driver', 'col-md-6 form-group','beanstalkd');
                $form .= $this->form($short, 'Host', 'host', 'col-md-6 form-group','localhost');
                $form .= $this->form($short, 'Queue', 'queue', 'col-md-6 form-group', 'default');
                $form .= "</div>";
                return $form;
            case "sqs":
                $form .= "<div class='row'>";
                $form .= $this->form($short, 'Driver', 'driver', 'col-md-6 form-group','sqs');
                $form .= $this->form($short, 'Key', 'key', 'col-md-6 form-group','your-public-key');
                $form .= $this->form($short, 'Secret', 'secret', 'col-md-6 form-group', 'your-queue-url');
                $form .= $this->form($short, 'Region', 'region', 'col-md-6 form-group', 'us-east-1');
                $form .= "</div>";
                return $form;
            case "iron":
                $form .= "<div class='row'>";
                $form .= $this->form($short, 'Driver', 'driver', 'col-md-6 form-group','iron');
                $form .= $this->form($short, 'Host', 'host', 'col-md-6 form-group','mq-aws-us-east-1.iron.io');
                $form .= $this->form($short, 'Token', 'token', 'col-md-6 form-group', 'your-token');
                $form .= $this->form($short, 'Project', 'project', 'col-md-6 form-group', 'your-project-id');
                $form .= $this->form($short, 'Queue', 'queue', 'col-md-6 form-group', 'your-queue-name');
                $form .= "</div>";
                return $form;
            case "redis":
                if(!extension_loaded('redis')) {
                    throw new Exception(trans('lang.extension_required_error', ['extension' => 'redis']), 500);
                }
                $form .= "<div class='row'>";
                $form .= $this->form($short, 'Driver', 'driver', 'col-md-6 form-group','redis');
                $form .= $this->form($short, 'Queue', 'queue', 'col-md-6 form-group', 'default');
                $form .= "</div>";
                return $form;
            default :
                return $form;
        }
        } catch(Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    public function form($short, $label, $name, $class, $placeholder = '') {
        $queueid = $this->getIdByShortName($short);
        $queues = new QueueService();
        $queue = $queues->find($queueid);
        if ($queue) {
            $form = "<div class='" . $class . "'>" . Form::label($name, $label) . "<span class='text-red'> *</span>" .
                    Form::text($name, $queue->getExtraField($name), ['class' => "form-control", 'placeholder' => $placeholder]) . "</div>";
        } else {
            $form = "<div class='" . $class . "'>" . Form::label($name, $label) . "<span class='text-red'> *</span>" .
                    Form::text($name, NULL, ['class' => "form-control", 'placeholder' => $placeholder]) . "</div>";
        }
        return $form;
    }

    /**
     * Handles record for horizon snapshot command in conditions table as the command
     * must only be available if the queue driver is redis. So if we provide redis as
     * QueueService then it will add the snapshot command else it will delete it from
     * conditions table.
     *
     * NOTE: This method just adds the record for snapshot command without scheduling it.
     * Means the cron job for this command will not be active.
     *
     * @param   QueueService $queue
     * @return  void
     */
    private function updateSnapShotJob(QueueService $queue):void
    {
        if($queue->short_name != 'redis') {
            Condition::where('job', 'queue-monitor-spanshot')->delete();
            return;
        }
        Condition::updateOrCreate(['job'=> 'queue-monitor-spanshot'],[
            'job'      => 'queue-monitor-spanshot',
            'active'    => 0,
            'icon'     => 'fa fa-camera',
            'command'  => 'horizon:snapshot',
            'job_info' => 'queue-monitor-spanshot-info',
        ]);
    }
}
