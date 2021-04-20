<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUpgradeNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['model_id', 'userid_created', 'type_id']);
            $table->string('message');
            $table->string('by');
            $table->string('to');
            $table->string('seen');
            $table->string('table')->default(null)->nullable();
            $table->integer('row_id')->default(null)->nullable();
            $table->string('url')->default(null)->nullable();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
