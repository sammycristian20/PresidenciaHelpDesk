<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApproverStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approver_statuses', function (Blueprint $table) {

            $table->increments('id');

            $table->integer('approver_id')->unsigned();

            $table->string('approver_type');

            $table->integer('approval_level_status_id')->unsigned();

            $table->string('status');

            $table->string('hash');

            $table->timestamps();
        });

        Schema::table('approver_statuses', function($table) {

            //NOTE: MYISAM db engine doesn't support foriegn keys. Model has to handle deletion
            $table->foreign('approval_level_status_id')->references('id')->on('approval_level_statuses')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approver_statuses');
    }
}
