<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveFormField extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:field {form} {--unique=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'removing field from any form builders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $form  = $this->argument('form');
        $uniques = $this->option('unique');
        $json_form = $this->formController()->getTicketFormJson($form);
        $array = json_decode($json_form,true);
        $check_array = checkArray(0, $array);
        if($uniques && $check_array){
            $array = collect($check_array)->whereNotIn('unique',$uniques)->toArray();
        }
        $request = \Illuminate\Http\Request::create(faveoUrl('forms'), 'POST', $array);
        $this->adminFormController()->store($request, $form);
        $this->info('Updated form');
    }
    
    public function formController(){
        return new \App\Http\Controllers\Utility\FormController();
    }
    public function adminFormController(){
        return new \App\Http\Controllers\Admin\helpdesk\FormController();
    }
}
