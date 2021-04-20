<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsernameInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $duplicateUser = $this->getDuplicateUsername();
            if(count($duplicateUser) == 0) {
                 $table->string('user_name')->unique()->change();
            }
        });
    }

    public function getDuplicateUsername()
    {
        $duplicateUsername = \DB::table('users')
        ->select('user_name', \DB::raw('COUNT(*) as `count`'))
        ->groupBy('user_name')
        ->having('count', '>', 1)
        ->get();

        return $duplicateUsername;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $duplicateUser = $this->getDuplicateUsername();
            if(count($duplicateUser) == 0) {
                $table->dropUnique('user_name');
                $table->dropIndex('users_user_name_unique');
            }
        });
    }
}
