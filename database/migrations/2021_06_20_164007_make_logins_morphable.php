<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeLoginsMorphable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logins', function (Blueprint $table) {
            $table->dropForeign('identifier');
            $table->dropForeign('type');
            $table->integer('user_id')->nullable()->index();
            $table->string('user_type')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logins', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('user_type');
        });
    }
}
