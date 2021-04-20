<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameApiEnpointInFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->renameColumn('api_endpoint', 'api_info');
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
            $table->renameColumn('api_info', 'api_endpoint');
        });
    }
}
