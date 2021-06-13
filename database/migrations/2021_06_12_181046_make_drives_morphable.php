<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeDrivesMorphable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drives', function (Blueprint $table) {
            $table->dropConstrainedForeignId('driver_in_id');
            $table->dropConstrainedForeignId('driver_out_id');
            $table->integer('driveable_in_id')->nullable()->index();
            $table->string('driveable_in_type')->nullable()->index();
            $table->integer('driveable_out_id')->nullable()->index();
            $table->string('driveable_out_type')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drives', function (Blueprint $table) {
            $table->dropColumn('driveable_in_id');
            $table->dropColumn('driveable_in_type');
            $table->dropColumn('driveable_out_id');
            $table->dropColumn('driveable_out_type');
        });
    }
}
