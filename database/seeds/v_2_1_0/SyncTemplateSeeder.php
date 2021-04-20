<?php

namespace database\seeds\v_2_1_0;

use App\Model\Common\TemplateSet;
use App\Model\Common\Template;
use database\seeds\DatabaseSeeder as Seeder;
use Logger;
use Exception;

/**
 * 
 */
class SyncTemplateSeeder extends Seeder
{
	/**
     * Writing all the artisan logs to this varible so that it could be returned in the end
     * @var string
     */
    private $log = '';

    public function run()
    {
    	try {
            $extraTemplateSets = $this->getExtraTemplateSet();
            if (!$extraTemplateSets) {
                return "No custom template set found. \r\n";
            }
            $count = count($extraTemplateSets);
            $this->log .= "$count custom set(s) found.\r\n";
            $allTemplates = Template::where('set_id', 1)->pluck('name')->toArray();
            foreach ($extraTemplateSets as $set) {
                $this->createMissingTemplates($set, $allTemplates);
            }

          return $this->log;
        } catch (Exception $ex) {
            $this->log = $this->log . "\n" . $ex->getMessage();
            Logger::exception($ex);
            return $this->log;
        }
    }

    /**
     * Function to get custom template sets created by user
     * @return Array  array containg id of user created template sets
     * or empty array
     */
    private function getExtraTemplateSet()
    {
        return TemplateSet::where('id', '>', 1)->pluck('id')->toArray();
    }


    /**
     * Function creates new or missing templates for user created template sets
     * @param   int    id of a set in which missing templates are being checked
     * @param   Array  array containing name of templates in default set
     * @return  void
     */
    private function createMissingTemplates(int $set, array $allTemplates):void
    {
        $templates = Template::where('set_id', $set)->pluck('name')->toArray();
        $missingTemplates = array_diff($allTemplates, $templates);
        if (!$missingTemplates) {
            $this->log .= "No missing template found in set id $set.\r\n ";
        }

        foreach ($missingTemplates as $missingTemplate) {
            $this->log .= "Creating template $missingTemplate for set id $set.\r\n ";
            $templates = Template::where('set_id',1)->where('name', $missingTemplate)->first();
            $newTemplate = $templates->replicate();
            $newTemplate->set_id = $set;
            $newTemplate->save();
            $this->log .= "Created template $missingTemplate for $set.\r\n ";
        }
    }
}