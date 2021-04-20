<?php

namespace database\seeds\v_2_2_7;

use App\Backup_path;
use database\seeds\DatabaseSeeder as Seeder;
use App\Model\Common\TemplateSet;
use App\Model\Common\TemplateShortCode;
use App\Model\Common\TemplateType;
use App\Model\Common\Template;
use App\Model\helpdesk\Settings\Alert;

class DatabaseSeeder extends Seeder
{

  /**
   * method to execute database seeds
   * @return void
   */
  public function run()
  {
    $this->backupPathSeeder();
    $this->alertSeeder();
    $this->templateShortCodesSeeder();
    $this->templateTypeSeeder();
    $this->templateSeeder();
  }

  private function backupPathSeeder()
  {
    $path = dirname(base_path(), 1).DIRECTORY_SEPARATOR.'storage';
    Backup_path::updateOrCreate(['id'=>1],['backup_path'=>$path]);
  }
  /**
    * Alert And Notice seeder
     * @return 
     */
    private function alertSeeder()
    {
      $alert = ['backup_completed'];
      $append = ['', '_mode', '_persons'];
      $values = [1, 'email,system', 'agent,admin'];
      for ($templateType = 0, $appendType = 0, $value = 0; $templateType < count($alert); $appendType++, $value++) { 
          if ($appendType < count($append)) {
              Alert::create(['key' => $alert[$templateType] . $append[$appendType], 'value' => $values[$value]]);
          }
          else {
              $appendType = -1;
              $value = -1;
              ++$templateType;
          }
      }
    } 

    /**
     * Template Short Codes Seeder
     * @return 
     */
    private function templateShortCodesSeeder()
    {
        TemplateShortCode::updateOrCreate(['key_name' => 'version'], [
          'shortcode'            => '{!! $version !!}',           
          'description_lang_key' => 'lang.shortcode_version_description',
          'key_name'             => 'version'
        ]);
    }

    /**
     * Template Type Seeder
     * @return 
     */
    private function templateTypeSeeder()
    {
      TemplateType::updateOrCreate(['name' => 'backup-completed'], ['name' => 'backup-completed']);
    }


    /**
     * Template Seeder
     * @return 
     */
    private function templateSeeder()
    {
      $template = [
        'name' => 'system-backup-completed',
          'variable' => 1,
          'type' => TemplateType::where('name', 'backup-completed')->first()->id,
          'subject' => 'Your system backup has been completed successfully',
          'message' => 'Hello {!! $receiver_name !!} <br/> <br/>'
                      .'Your system backup for {!! $version !!} has been completed successfully <br/> <br/> '
                      .'Kind Regards, <br>'
                      .'{!! $company_name !!}',
          'description' => 'template to send notification for system backup completion',
          'template_category' => 'agent-templates'
        ];
      $setIds = TemplateSet::all()->pluck('id')->toArray();

      for ($setId = 0; $setId < count($setIds); $setId++)
      {
          $template['set_id'] = $setIds[$setId];
          Template::updateOrCreate($template, $template);
      }
    }

}
