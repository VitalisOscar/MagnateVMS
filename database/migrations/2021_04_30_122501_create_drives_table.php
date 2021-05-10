<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_out_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('driver_in_id')->constrained('drivers')->cascadeOnDelete();
            $table->dateTime('time_out')->useCurrent();
            $table->dateTime('time_in')->nullable();
            $table->integer('fuel_out');
            $table->integer('fuel_in')->nullable();
            $table->integer('mileage_out');
            $table->integer('mileage_in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drives');
    }
}
