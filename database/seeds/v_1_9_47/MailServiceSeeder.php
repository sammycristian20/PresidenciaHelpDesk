<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
//model
use App\Model\MailJob\MailService;

class MailServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('mail_services')->truncate();
        $mail = new MailService();
        $services= ['smtp'=>'SMTP','mail'=>'Php Mail','sendmail'=>'Send Mail','mailgun'=>'Mailgun','mandrill'=>'Mandrill','log'=>'Log file','mailrelay'=>'Mailrelay'];
        foreach($services as $key=>$value){
        $mail->create([
            'name'=>$value,
            'short_name'=>$key,
        ]);
        }
    }
}
