<?php

namespace database\seeds\v_3_4_2;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Form\FormCategory;

class DatabaseSeeder extends Seeder
{
    /**
     * method to execute database seeds
     * @return void
     */
    public function run()
    {
        $this->ticketFormFieldsUpdateSeeder();
    }

    /**
     * method to update api end point in ticket form fields and type as "api"
     * @return null
     */
    private function ticketFormFieldsUpdateSeeder()
    {
        $categoryId = FormCategory::where('category', 'ticket')->first()->id;
        FormField::where([['category_id', $categoryId], ['title', 'Status']])->first()->update(['type' => 'api', 'api_info' => 'url:=/api/dependency/statuses;;']);
        FormField::where([['category_id', $categoryId], ['title', 'Priority']])->first()->update(['type' => 'api', 'api_info' => 'url:=/api/dependency/priorities;;']);
        FormField::where([['category_id', $categoryId], ['title', 'Location']])->first()->update(['type' => 'api', 'api_info' => 'url:=/api/dependency/locations;;']);
        FormField::where([['category_id', $categoryId], ['title', 'Type']])->first()->update(['type' => 'api', 'api_info' => 'url:=/api/dependency/types;;']);
        FormField::where([['category_id', $categoryId], ['title', 'Source']])->first()->update(['type' => 'api', 'api_info' => 'url:=/api/dependency/sources;;']);
        FormField::where([['category_id', $categoryId], ['title', 'Requester']])->update(['api_info' => 'url:=/api/dependency/users?meta=true;;']);
        FormField::where([['category_id', $categoryId], ['title', 'CC']])->first()->update(['type' => 'api', 'api_info' => 'url:=/api/dependency/users?meta=true;;']);
        FormField::where([['category_id', $categoryId], ['title', 'Help Topic']])->first()->update(['type' => 'api', 'api_info' => 'url:=/api/dependency/help-topics?meta=true;;']);
        FormField::where([['category_id', $categoryId], ['title', 'Department']])->first()->update(['type' => 'api', 'api_info' => 'url:=/api/dependency/departments?meta=true;;']);
        FormField::where([['category_id', $categoryId], ['title', 'Assigned']])->first()->update(['type' => 'api', 'api_info' => 'url:=/api/dependency/agents?meta=true;;']);
        FormField::where('title', 'Attachment')->update(['title' => 'Attachments']);
    }
}
