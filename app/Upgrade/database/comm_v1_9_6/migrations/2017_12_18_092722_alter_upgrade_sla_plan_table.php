<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUpgradeSlaPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sla_plan', function (Blueprint $table) {
            $table->integer('sla_target');
            $table->string('apply_sla_depertment');
            $table->string('apply_sla_company');
            $table->string('apply_sla_tickettype');
            $table->string('apply_sla_ticketsource');
            $table->string('apply_sla_helptopic');
            $table->string('apply_sla_orgdepts');
            $table->string('apply_sla_labels');
            $table->string('apply_sla_tags');
            $table->boolean('is_default')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
