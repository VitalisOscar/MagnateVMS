<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffCheckInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->dateTime('time_in')->useCurrent();
            $table->dateTime('time_out')->nullable();
            $table->string('car_registration')->nullable();
            $table->foreignId('checked_in_by')->constrained('users', 'id');
            $table->foreignId('checked_out_by')->nullable()->constrained('users', 'id');
            $table->foreignId('site_id')->constrained('sites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_check_ins');
    }
}
