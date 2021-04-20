<?php

namespace App\Plugins\LimeSurvey\Controllers;

use Schema;
use Illuminate\Http\Request;
use App\Model\Common\Template;
use App\Model\Common\TemplateSet;
use App\Model\Common\TemplateType;
use App\Http\Controllers\Controller;
use App\Plugins\LimeSurvey\Model\LimeSurvey;

class LimeSurveySetting extends Controller {


   private $limeSurvey;

   public function __construct(){
      $this->limeSurvey = new LimeSurvey;
   }


   public function activate(){
      try{
         if (!Schema::hasTable('lime_survey')) {
                $path = "app" . DIRECTORY_SEPARATOR . "Plugins" . DIRECTORY_SEPARATOR . "LimeSurvey" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "migration";
                \Artisan::call(
                    'migrate', [
                '--path' => $path,
                '--force' => true,
                    ]
                );
                $this->seedTemplates();
            }
      }
      catch(\Exception $e){

      }
   }

   public function getSettings(){
      $survey = $this->limeSurvey->first();
      return view("LimeSurvey::survey.settings", compact('survey'));
   }

   public function setSettings(Request $request){
      try{
         $data = $request->except(['_token', '_method']);
         $survey = $this->limeSurvey->first();
         if($survey)
            $survey->update($data);
         else
            $survey = $this->limeSurvey->create($data);
         
         if($survey)
            return view("LimeSurvey::survey.settings", compact('survey'))->with('status', 'survey link updated successfully');
         else
            return view("LimeSurvey::survey.settings", compact('survey'))->with('error', 'failed to save/update. Please try again');
      }
      catch(\Exception $e){
         return view("LimeSurvey::survey.settings")->with('error', $e->getMessage());
      }
   }

   public function seedTemplates(){
      try{
         $template_type = TemplateType::updateOrCreate(['name' => 'lime-survey']);

         \Log::info("Seeding");
         \Log::info("\n\n---------------------------\n\n");
         \Log::info($template_type->toArray());
         \Log::info("\n\n---------------------------\n\n");

         if($template_type->id){
            Template::updateOrCreate([
               'variable' => '1',
               'template_category' => 'common-templates',
               'subject' => 'Helpdesk Feedback',
               'name' => 'lime-survey',
               'type' => $template_type->id,
               'set_id' => '1',
               'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                           ."We are constantly trying to improve customer experience. That's why we would like <b>5 minutes </b>of your time to complete this survey. Would be great if you could complete the survey at the earliest.<br /><br />"
                           .'<a href={!! $survey_link !!}><b> TAKE SURVEY NOW </b></a><br/><br />'
                           .''
                           .'Kind Regards,<br />'
                           .'{!! $system_from !!}'
           ]);
         }
      }
      catch(\Exception $e){
         \Log::info("Exception caught :  ".$e->getMessage());
      }
   }
}

?>