<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSettingsFilesystemTableToAddFilesMovedFromOldPrivateDisk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_filesystem', function (Blueprint $table) {
            $table->tinyInteger('files_moved_from_old_private_disk')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings_filesystem', function (Blueprint $table) {
            $table->dropColumn('files_moved_from_old_private_disk');
        });
    }
}
