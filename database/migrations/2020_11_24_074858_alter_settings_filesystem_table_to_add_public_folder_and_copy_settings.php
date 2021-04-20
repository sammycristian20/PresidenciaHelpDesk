<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSettingsFilesystemTableToAddPublicFolderAndCopySettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_filesystem', function (Blueprint $table) {
            $table->tinyInteger('show_public_folder_with_default_disk')->default(0);
            $table->tinyInteger('paste_on_disk_change')->default(0);
            $table->string('paste_type')->nullable();
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
            $table->dropColumn(['show_public_folder_with_default_disk','paste_on_disk_change','paste_type']);
        });
    }
}
