<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserRelationsToDrives extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drives', function (Blueprint $table) {
            $table->foreignId('checked_out_by')->nullable()->constrained('users', 'id');
            $table->foreignId('checked_in_by')->nullable()->constrained('users', 'id');
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
            //
        });
    }
}
