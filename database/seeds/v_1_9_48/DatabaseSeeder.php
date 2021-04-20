<?php

namespace database\seeds\v_1_9_48;

use App\Model\helpdesk\Ticket\Ticket_Status as TicketStatus;
use App\Model\Common\TemplateType;
use App\Model\Common\Template;
use App\Model\Common\TemplateSet;
use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Settings\CommonSettings;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Reports Settings Seeder
        $this->reportsSettingsSeeder();

        $this->ticketStatusSeeder();

        $this->templateSeeder();

    }

    /**
     * Ticket status seeder
     * @return null
     */
    private function ticketStatusSeeder()
    {
      TicketStatus::where([['purpose_of_status', '=', 1],['default', '=', '1']])->update(['auto_close' => 1]);
    }

    /**
     * Report Settings Seeder
     * @return null
     */
    private function reportsSettingsSeeder()
    {
        // Reports max date range
        CommonSettings::updateOrCreate(['option_name' => 'reports_max_date_range'], ['option_value' => 2]);

        // Reports records per file
        CommonSettings::updateOrCreate(['option_name' => 'reports_records_per_file'], ['option_value' => 1000]);
    }

    /**
     * Template seeder
     * @return null
     */
    private function templateSeeder()
    {
      Template::where('name', 'invoice')->update(['name' => 'template-invoice-to-clients']);
      Template::where('name', 'rating')->update(['name' => 'template-rating-feedback']);
      Template::where('name', 'rating-confirmation')->update(['name' => 'template-rating-confirmation', 'template_category' => 'agent-templates']);
      Template::where('name', 'ticket-approval')->update(['name' => 'template-ticket-approval']);
      Template::where('name', 'invoice')->update(['name' => 'template-invoice-to-clients']);

      $template_type = TemplateType::updateOrCreate(['name' => 'error-notification'], ['name' => 'error-notification']);
      $sets = TemplateSet::all()->pluck('id')->toArray();
      foreach ($sets as $set) {
          Template::updateOrCreate(['type' => $template_type->id, 'set_id' => $set],[
              'variable' => '1',
              'template_category' => 'agent-templates',
              'subject' => 'Helpdesk error notification',
              'name' => 'template-error-notification',
              'type' => $template_type->id,
              'set_id' => $set,
              'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                          .'{!! $error_message !!}.<br /><br />'
                          .'<strong>Details</strong><br />'
                          .'Message: {!! $ex_message !!}<br />'
                          .'File: {!! $ex_file !!}<br />'
                          .'Line: {!! $ex_line !!}<br />'
                          .'Trace: <blockquote>{!! $ex_trace !!}</blockquote><br /><br />'
                          .'Kind Regards,<br />'
                          .'{!! $system_from !!}'
          ]);
      }
    }
}
