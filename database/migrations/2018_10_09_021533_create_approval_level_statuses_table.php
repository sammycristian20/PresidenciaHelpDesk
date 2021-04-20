<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalLevelStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_level_statuses', function (Blueprint $table) {

            $table->increments('id');

            //might not be needed
            $table->integer('approval_level_id')->unsigned();

            $table->integer('approval_workflow_ticket_id')->unsigned();

            $table->string('name', 255);

            $table->string('match', 10);

            $table->tinyInteger('order')->unsigned();

            $table->boolean('is_active');

            $table->string('status');

            $table->timestamps();
        });

        Schema::table('approval_level_statuses', function($table) {

            //NOTE: MYISAM db engine doesn't support foriegn keys. Model has to handle deletion
            $table->foreign('approval_level_id')->references('id')->on('approval_levels')->onDelete('cascade');;

            $table->foreign('approval_workflow_ticket_id')->references('id')->on('approval_workflow_tickets')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_level_statuses');
    }
}
