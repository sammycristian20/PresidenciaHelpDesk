<?php

namespace database\seeds\v_2_1_0;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Ticket\TicketFilterMeta;
use DB;

class FilterMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->migrateOldFiltersToNew();
    }

    private function migrateOldFiltersToNew()
    {
      // disabling foriegn key check since we are updating all the entries in child table.
      // relation with parent is irrelevant in this case.
      DB::statement('SET FOREIGN_KEY_CHECKS=0');

      // take data from value_meta column, extract, ids and values and update
      // in value column
      $ticketFilterMeta = TicketFilterMeta::get();
      foreach ($ticketFilterMeta as $filter) {
        $filter->value = $this->extractValueOutOfMeta($filter->value_meta);
        $filter->save();
      }
    }

    /**
     * Extracts value of out value_meta and give the actual value.
     * For eg. value_meta is [{id: 1, name: testOne},{id: 2, name: testTwo}]
     * value will be [1, 2]
     * @param  string|array $valueMeta
     * @return string|array
     */
    private function extractValueOutOfMeta($valueMeta)
    {
      // if it is an array
      if(is_array($valueMeta)){

        if(array_key_exists('id', $valueMeta)){
          return $valueMeta['id'];
        }
        // it will be array of associated arrays. In that case it should be
        // able to extract all the ids
        $value = [];
        foreach ($valueMeta as $element) {
          if(array_key_exists('id', $element)){
            $value[] = $element['id'];
          }
        }
        return $value;
      }
      return $valueMeta;
    }
}
