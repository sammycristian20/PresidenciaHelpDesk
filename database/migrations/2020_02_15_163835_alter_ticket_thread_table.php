<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTicketThreadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // by default migration is done with utf8 character set and it doesn't support emojis, so changing it to utf8mb4
        DB::unprepared('ALTER TABLE `ticket_thread` CONVERT TO CHARACTER SET utf8mb4');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE `ticket_thread` CONVERT TO CHARACTER SET utf8');
    }
}
