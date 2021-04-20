<?php

namespace database\seeds\v_1_9_50;

use App\FaveoLog\database\seeds\LogSeeder;
use database\seeds\DatabaseSeeder as Seeder;
use App\Model\Common\Template;
use App\Model\Common\TemplateSet;
use App\Model\Common\TemplateType;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // has to be added by version-wise
      $this->call(LogSeeder::class);
      $this->updateTemplate();
    }

    private function updateTemplate()
    {
        $sets = TemplateSet::all()->pluck('id')->toArray();
        $type  = TemplateType::updateOrCreate(['name' => 'create-ticket-by-agent'], ['name' => 'create-ticket-by-agent'])->id;
        if($this->checkTemplateExists($type, 'client-templates') == 0)
        {
            $name = 'template-ticket-creation-acknowledgement-client-by-agent';
            $message = 'Hello {!! $receiver_name !!}<br /><br />'
                    .'Thank you for contacting us. This is to confirm that our agent has logged your request in our support system. When replying, please make sure that the ticket ID is kept in the subject so that we can track your replies.<br /><br />'
                    .'<strong>Ticket ID:</strong> {!! $ticket_number !!} <br /><br />'
                    .'<strong>Request message:</strong> {!! $message_content !!} <br /><br />'
                    .'{!! $department_signature !!}<br />'
                    .'You can check the status of or update this ticket online at: {!! $system_link !!}<br /><br />'
                    .'Kind Regards,<br />'
                    .'{!! $system_from !!}</p>';
            $this->createTemplateWithGivenCategory($sets, $type, 'client-templates', $message, $name);
        }

        if($this->checkTemplateExists($type, 'org-mngr-templates') == 0)
        {
            $name = 'template-ticket-creation-acknowledgement-org-mngr-by-agent';
            $message = '<p>Hello {!! $receiver_name !!},<br/><br/>'
                        .'Our agent has created a new ticket for a request of your organization&#39;s member.</p>'
                        .'<p><strong>Ticket ID:</strong>&nbsp; {!! $ticket_number !!}<br/><br/>'
                        .'<strong>Member&#39;s Details</strong><br/>'
                        .'<strong>Name:</strong>&nbsp; {!! $client_name !!}<br/>'
                        .'<strong>E-mail:</strong> {!! $client_email !!}<br/><br/>'
                        .'<strong>Message</strong><br/>'
                        .'{!! $message_content !!}<br/><br/>'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}</p>';
            $this->createTemplateWithGivenCategory($sets, $type, 'org-mngr-templates', $message, $name);
        }
    }

    /**
     * Function checks if template for create ticket by agent exists with given category
     * @param  String  cateory name
     * @return Integer 0 if does exit else count of templates found with category
     */
    protected function checkTemplateExists(int $type, String $category)
    {
        return Template::where([['type', '=', $type],['template_category', '=', $category]])->count();
    }

    /**
     * Function to create given Template for all sets
     * @param  Array   $setIds    Array containing set ids
     * @param  int     $type      type of template
     * @param  String  $category  Category of template
     * @param  String  $message   message body of template
     * @param  String  $name      name of template
     */
    protected function createTemplateWithGivenCategory(Array $setIds, int $type, String $category, String $message, String $name)
    {
        foreach ($setIds as $setId) {
            Template::updateOrCreate(['type' => $type, 'set_id' => $setId, 'template_category' => $category],[
                'variable' => '0',
                'name' => $name,
                'type' => $type,
                'template_category' => $category,
                'set_id' => $setId,
                'subject' => '',
                'message' => $message
            ]);
        }
    }
}
