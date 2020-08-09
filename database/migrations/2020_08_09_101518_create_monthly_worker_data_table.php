<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyWorkerDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_worker_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->unsignedSmallInteger('hours_worked')->nullable();
            $table->unsignedSmallInteger('hours_to_be_worked')->nullable();
            $table->unsignedSmallInteger('hourly_rate')->nullable();
            $table->unsignedSmallInteger('bonus')->nullable();
            $table->timestamp('month');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_worker_data');
    }
}
