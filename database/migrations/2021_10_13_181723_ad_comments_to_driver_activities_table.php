<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdCommentsToDriverActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_activities', function (Blueprint $table) {
            $table->string('comments')->after('task')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_activities', function (Blueprint $table) {
            $table->dropColumn('comments');
        });
    }
}
