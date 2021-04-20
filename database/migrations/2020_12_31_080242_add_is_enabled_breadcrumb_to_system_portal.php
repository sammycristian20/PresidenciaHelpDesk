<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsEnabledBreadcrumbToSystemPortal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_portal', function (Blueprint $table) {
            $table->boolean('is_enabled_breadcrumb')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_portal', function (Blueprint $table) {
            $table->dropColumn('is_enabled_breadcrumb');
        });
    }
}
