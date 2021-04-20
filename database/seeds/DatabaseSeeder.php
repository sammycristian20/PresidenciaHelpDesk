<?php

namespace database\seeds;

use Illuminate\Database\Seeder;

/**
 * Base Seeder class to which all seeders should extend
 *
 * @package database\seeds
 * @author Manish Verma <manish.verma@ladybirdweb.com>
 * @since v3.X
 */
class DatabaseSeeder extends Seeder
{
	public function __construct()
	{
		/**
		 * Enables mass assignment protection during seeding database
		 * which is disabled for seeders by default
		 * @see https://laravel.com/docs/6.x/seeding
		 */
		\Illuminate\Database\Eloquent\Model::reguard();
	}
}
