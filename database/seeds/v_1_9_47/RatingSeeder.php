<?php

namespace database\seeds\v_1_9_47;

use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Ratings\Rating;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Ratings
         */
        Rating::updateOrCreate(['id' => '1', 'name' => 'OverAll Satisfaction', 'display_order' => '1', 'allow_modification' => '1', 'rating_scale' => '5', 'rating_area' => 'Helpdesk Area', 'rating_icon' => 'star']);
        Rating::updateOrCreate(['id' => '2', 'name' => 'Reply Rating', 'display_order' => '2', 'allow_modification' => '1', 'rating_scale' => '5', 'rating_area' => 'Comment Area']);

    }
}
