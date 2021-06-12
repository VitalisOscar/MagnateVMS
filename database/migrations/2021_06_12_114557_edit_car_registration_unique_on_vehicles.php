<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditCarRegistrationUniqueOnVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table){
            $table->dropIndex('vehicles_registration_no_unique');
            $table->unique(['registration_no', 'vehicleable_id', 'vehicleable_type'], 'holder');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table){
            $table->unique('registration_no');
            $table->dropIndex('holder');
        });
    }
}
