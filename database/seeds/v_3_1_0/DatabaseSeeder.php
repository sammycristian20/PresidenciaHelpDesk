<?php


namespace database\seeds\v_3_1_0;

use App\Model\helpdesk\Ticket\Ticket_source;
use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Form\FormFieldLabel;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Form\FormCategory;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(ReportSeeder::class);
        $this->seedDefaultSources();
        $this->nestedFormFieldTitleSeeder();
        $this->labelMigrationSeeder();
        $this->formCategorySeeder();
    }

    private function seedDefaultSources()
    {
        Ticket_source::whereRaw("upper(name) IN ('WEB','EMAIL','AGENT','FACEBOOK','TWITTER','CALL','CHAT','WHATSAPP')")->update(['is_default' => 1]);
    }

    /**
     * method to updating title for nested form field
     * Nested Select => Select
     * Nested Radio => Radio
     * Nested Checkbox => Checkbox
     * @return void
     */
    private function nestedFormFieldTitleSeeder()
    {
        $formFieldTitles = ['Select', 'Checkbox', 'Radio'];
        foreach ($formFieldTitles as $title) {
            FormField::where('title', "Nested $title")->update(['title' => $title]);
        }
    }

    /**
     * Removes client labels(since client and agent share the same label from this version)
     * and migrates agent labels to form field label
     */
    private function labelMigrationSeeder()
    {
        // deleting all user's label
        // NOTE: all form fields will have agent label but they might not have user label
        FormFieldLabel::where("meant_for", "user")->delete();
        FormFieldLabel::where("meant_for", "agent")->update(["meant_for"=>"form_field"]);
    }

    /** 
     * method to add category name in form categories table
     * @return null
     */
    private function formCategorySeeder()
    {
        $formCategory = new FormCategory();
        $formCategory->where('category', 'ticket')->update(['name' => 'Ticket']);
        $formCategory->where('category', 'organisation')->update(['name' => 'Organization']);
        $formCategory->where('category', 'user')->update(['name' => 'Requester']);
    }
}