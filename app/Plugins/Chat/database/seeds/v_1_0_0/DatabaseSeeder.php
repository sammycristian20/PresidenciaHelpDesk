<?php

namespace App\Plugins\Chat\database\seeds\v_1_0_0;

use Illuminate\Database\Seeder;
use App\Plugins\Chat\Model\Chat;

class DatabaseSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $this->seed();
    }

    private function seed()
    {
        $platforms = $this->defaultChatPlatforms();
        foreach ($platforms as $key => $value) {
            Chat::updateOrCreate(
                ['short' => $key],
                [
                    'short' => $key, 
                    'name' => $value['name'], 
                    'secret_key_required' => $value['secret_key_required'],
                    'status' => $value['status']
                ]
            );
        }
    }

    private function defaultChatPlatforms(){
        return [
            'liv_serv' => ['name' => 'LivServ', 'secret_key_required' => 0,'status' => 0],
        ];
    }
}
