<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visits', function(Blueprint $table){
            $table->text('items_in')->nullable()->after('time_in');
            $table->text('items_out')->nullable()->after('time_in');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visits', function(Blueprint $table){
            $table->dropColumn('items_in');
            $table->dropColumn('items_out');
        });
    }
}
