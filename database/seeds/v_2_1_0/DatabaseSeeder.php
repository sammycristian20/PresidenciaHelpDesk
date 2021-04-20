<?php

namespace database\seeds\v_2_1_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Ticket\TicketFilterMeta;
use App\FaveoLog\Model\LogCategory;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FilterMigrationSeeder::class);
        $this->call(SyncTemplateSeeder::class);
        $this->logCategorySeeder();
    }

    /**
     * Add mail-send entry to LogCategory
     * @return void
     */
    private function logCategorySeeder() {
        LogCategory::updateOrCreate(['name' => 'mail-send']);
    }
}
