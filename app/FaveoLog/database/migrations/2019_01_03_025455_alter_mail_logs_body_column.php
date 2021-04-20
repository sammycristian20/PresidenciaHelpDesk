<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMailLogsBodyColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('mail_logs', function (Blueprint $table) {
          $table->text('body', 65535)->change();
      });

      \DB::statement('ALTER TABLE `mail_logs` MODIFY `body` LONGBLOB');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('mail_logs', function (Blueprint $table) {
          $table->string('body')->change();
      });
    }
}
