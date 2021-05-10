<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeDriverInFieldNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drives', function (Blueprint $table) {
            $table->dropForeign('drives_driver_in_id_foreign');
            $table->foreignId('driver_in_id')->nullable()->change()->constrained('drivers')->nullOnDelete();
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
