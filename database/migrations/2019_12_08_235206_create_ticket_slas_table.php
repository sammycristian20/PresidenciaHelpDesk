<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketSlasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_slas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('status');
            $table->integer('order');
            $table->string('matcher');
            $table->boolean('is_default');
            $table->longText('internal_notes')->nullable();
            $table->timestamps();
        });

        // deleting old sla foreign keys
        // for some clients this foriegn key might not exists, since we used to use SQL to do that and in SQL
        // this key is missing. Since there are no foreign key option available in laravel for checking its existenece,
        // we are putting this in a try catch block
        try {
            Schema::table('tickets', function (Blueprint $table) {
                // this key has been created on sla_plan column
                $table->dropForeign('tickets_ibfk_5');
            });
        } catch(Exception $e){
            // do nothing
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_slas');
    }
}
