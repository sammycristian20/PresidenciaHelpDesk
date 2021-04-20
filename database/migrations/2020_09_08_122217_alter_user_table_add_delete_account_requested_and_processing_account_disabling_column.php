<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserTableAddDeleteAccountRequestedAndProcessingAccountDisablingColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            /**
             * can be used to get account delete request from client/users
             * and delete their account.
             */
            $table->boolean('delete_account_requested')->default(0);
            /**
             * While deactivating and deleting the account we will not directly
             * move them to delete or deactivation as the process requires to handle
             * dependent models so we will use this column to store processing request for
             * deletion or deactivation and perform the actions in background via job.
             */
            $table->boolean('processing_account_disabling')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('delete_account_requested');
            $table->dropColumn('processing_account_disabling');
        });
    }
}
