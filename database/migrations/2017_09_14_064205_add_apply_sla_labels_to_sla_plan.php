<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApplySlaLabelsToSlaPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('sla_plan', function (Blueprint $table) {
            $table->string('apply_sla_labels');
            $table->string('apply_sla_tags');
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
             $table->dropColumn('apply_sla_labels');
            $table->dropColumn('apply_sla_tags');
        });
    }
}
