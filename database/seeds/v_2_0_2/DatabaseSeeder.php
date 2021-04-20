<?php

namespace database\seeds\v_2_0_2;

use database\seeds\DatabaseSeeder as Seeder;
use DB;
use App\Model\helpdesk\Utility\CountryCode;
use App\Model\Common\Template;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->updateCountryCodeOfRussia();
        $this->updateTicketApprovalTemplate();
    }

    /**
     * Update country code of Russia
     * @return void
     */
    private function updateCountryCodeOfRussia() {
      CountryCode::where('name', 'RUSSIAN FEDERATION')->update(['phonecode' => 7]);
    }

    /**
     * Update ticket approval template for fresh install
     * @return void
     */
    private function updateTicketApprovalTemplate() {
        Template::where('name', 'template-ticket-approval')->whereColumn('created_at', 'updated_at')->update([
            'message' => '<p>Hello {!! $receiver_name !!},<br /><br />'
                        .'Ticket <a href="{!! $ticket_link !!}">{!! $ticket_number !!}</a> has been waiting for your approval.<br /><br />'
                        .'Please click <a href={!! $approval_link !!}>here</a> to review the ticket.<br /><br />'
                        .'Have a nice day.<br /><br />'
                        .'Kind Regards,<br />'
                        .'{!! $system_from !!}'
        ]);
    }

}
