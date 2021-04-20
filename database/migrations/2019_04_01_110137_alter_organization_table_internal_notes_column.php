<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrganizationTableInternalNotesColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('organization', function (Blueprint $table) {
        $table->string('internal_notes')->nullable()->change();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('organization', function (Blueprint $table) {
        $table->string('internal_notes')->change();
      });
    }
}
