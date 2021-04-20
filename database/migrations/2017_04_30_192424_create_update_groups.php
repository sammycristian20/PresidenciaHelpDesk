<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //echo $e;
        Schema::table('groups', function (Blueprint $table) {
            
             $table->string('can_upload_document');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('groups', function (Blueprint $table) {
           $table->dropIfExists('can_upload_document');
        });
    }
}
