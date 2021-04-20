<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;
use App\Location\Models\Location;


class AlterUserTableLocation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //change location name to id
        $this->changeLocationNameToId();

        Schema::table('users', function (Blueprint $table) {
            $table->integer('location')->nullable()->charset('')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('location')->change();
        });
    }

    /**
     * This method convert location id based on location name
     *
     */
    public function changeLocationNameToId()
    {

        $userLocations = User::whereNotNull('location')->where('location','!=',"")->select('id', 'location')->get()->toArray();

        if ($userLocations) {
            foreach ($userLocations as $userLocation) {
                //get location name to id
                $locationId = Location::where('title', $userLocation['location'])->value('id');
                //update location id in user table
                User::where('id', $userLocation['id'])->update(['location' => $locationId]);
            }
        }
        return true;
    }
}
