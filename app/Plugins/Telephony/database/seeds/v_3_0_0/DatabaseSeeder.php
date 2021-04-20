<?php

namespace App\Plugins\Telephony\database\seeds\v_3_0_0;
//old models
use database\seeds\DatabaseSeeder as Seeder;
//New models
use App\Plugins\Telephony\Model\TelephonyLog;
use App\Plugins\Telephony\Model\TelephonyProvider;
use App\Model\MailJob\Condition;
use DB;
use Schema;

class DatabaseSeeder extends Seeder
{
	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->updateCallProviders();
        $this->createCronJob();
        $this->changeMcubeShort();
        $this->seedProviders();
    }

    private function updateCallProviders()
    {
        if(Schema::hasTable('telephone_providers') + Schema::hasTable('telephone_details') == 0) {
            return;
        }
    	$providers = DB::table('telephone_providers')->get(['name', 'short'])->toArray();
    	foreach ($providers as $provider) {
    		$settings = DB::table('telephone_details')->where('provider', $provider->short)->whereIn('key',['iso', 'token' , 'log_miss_call'])->get()->toArray();
    		foreach ($settings as $setting) {
    		    $provider->{$setting->key} = $setting->value;
    		}
    		TelephonyProvider::updateOrCreate(['name'=>$provider->name],(array)$provider);
    	}
    }

    private function createCronJob()
    {
        $checkValue =  Condition::where('job','call-conversion')->value('value');

        if(!$checkValue) {
            Condition::create([
                "job"=>"call-conversion", "value"=>"everyTenMinutes",
                "icon" => "fas fa-phone", "command" => "telephony:convert",
                "job_info" => "telephony-conversion-info", "plugin_job" => 1,
                "plugin_name" => "Telephony",
                'active'=>1,
            ]);
        }
    }

    private function changeMcubeShort()
    {
        TelephonyProvider::where('short', 'm_cube')->update(['short' => 'mcube']);
    }

    private function seedProviders()
    {
        $providers = $this->getAvaialbleProviders();
        foreach ($providers as $key => $value) {
            TelephonyProvider::updateOrCreate(
                ['short' => $key],
                ['short' => $key, 'name' => $value]
            );
        }
    }

    private function getAvaialbleProviders(){
        return [
            'exotel' => 'Exotel',
            'mcube' => 'MCube',
            'myoperator' => 'MyOperator',
            'knowlarity' => 'Knowlarity'
        ];
    }
}
