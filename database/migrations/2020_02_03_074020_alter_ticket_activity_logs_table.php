<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTicketActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->renameColumn('final_value', "value");
            $table->renameColumn('type_id', "ticket_id");
            $table->dropColumn("type");
            $table->dropColumn("initial_value");
            $table->bigInteger("parent_id")->nullable();
            $table->string("category", 100);

            $table->dropColumn("is_created");
            $table->dropColumn("is_updated");
            $table->dropColumn("is_deleted");

            // unique identifier which comes with request
            $table->string("identifier")->nullable();
        });

        Schema::table('ticket_activity_logs', function (Blueprint $table) {
            $table->string("value")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_activity_logs', function (Blueprint $table) {
            $table->integer('id')->change();
            $table->renameColumn('value', "final_value");
            $table->renameColumn('ticket_id', "type_id");
            $table->string('initial_value');
            $table->string('type');
            $table->dropColumn("parent_id");
            $table->boolean("is_created");
            $table->boolean("is_updated");
            $table->boolean("is_deleted");
            $table->dropColumn("identifier");
        });

        Schema::table('ticket_activity_logs', function (Blueprint $table) {
            $table->string("final_value")->change();
        });
    }
}
