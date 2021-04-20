<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAgentAndUserConfigColumnsToFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->boolean('is_agent_config')->default(1);
            $table->boolean('is_user_config')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropColumn('is_agent_config');
            $table->dropColumn('is_user_config');
        });
    }
}
