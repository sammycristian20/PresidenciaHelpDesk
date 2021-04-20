<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\MailJob\QueueService;

class QueueServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $queue = new QueueService();
        $services = [
            'sync'=>'Sync',
            'database'=>'Database',
            'beanstalkd'=>'Beanstalkd',
            'sqs'=>'SQS','iron'=>'Iron',
            'redis'=>'Redis'
        ];
        foreach ($services as $key => $value) {
            $queue->create([
                'name'=>$value,
                'short_name'=>$key,
                'status'=>0,
            ]);
        }
        $q = $queue->where('short_name', 'sync')->first();
        if ($q) {
            $q->status = 1;
            $q->save();
        }
    }
}
