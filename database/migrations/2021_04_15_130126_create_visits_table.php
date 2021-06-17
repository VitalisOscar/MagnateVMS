<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors');
            $table->foreignId('staff_id')->constrained('staff');
	        $table->foreignId('site_id')->constrained('sites');
            $table->string('reason');
            $table->dateTime('time_in')->useCurrent();
            $table->dateTime('time_out')->nullable();
            $table->string('car_registration')->nullable();
            $table->string('from');
            $table->string('card_number');
            $table->string('signature');
            $table->foreignId('checked_in_by')->constrained('users', 'id');
            $table->foreignId('checked_out_by')->nullable()->constrained('users', 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visits');
    }
}
