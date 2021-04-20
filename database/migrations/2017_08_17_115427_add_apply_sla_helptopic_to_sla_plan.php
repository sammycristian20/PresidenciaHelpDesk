<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApplySlaHelptopicToSlaPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sla_plan', function (Blueprint $table) {
            $table->string('apply_sla_helptopic');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sla_plan', function (Blueprint $table) {
            $table->dropColumn('apply_sla_helptopic');
        });
    }
}
