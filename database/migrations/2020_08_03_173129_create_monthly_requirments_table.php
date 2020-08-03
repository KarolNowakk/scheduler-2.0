<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyRequirmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_requirments', function (Blueprint $table) {
            $table->timestamps();

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unsignedBigInteger('work_place_id');
            $table->timestamp('month');

            $table->time('monday_start');
            $table->time('monday_end');
            $table->integer('monday_workers_on_shift');
            $table->time('tuesday_start');
            $table->time('tuesday_end');
            $table->integer('tuesday_workers_on_shift');
            $table->time('wednesday_start');
            $table->time('wednesday_end');
            $table->integer('wednesday_workers_on_shift');
            $table->time('thursday_start');
            $table->time('thursday_end');
            $table->integer('thursday_workers_on_shift');
            $table->time('friday_start');
            $table->time('friday_end');
            $table->integer('friday_workers_on_shift');
            $table->time('saturday_start');
            $table->time('saturday_end');
            $table->integer('saturday_workers_on_shift');
            $table->time('sunday_start');
            $table->time('sunday_end');
            $table->integer('sunday_workers_on_shift');
           
            $table->foreign('work_place_id')
                ->references('id')
                ->on('work_places')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monthly_requirments');
    }
}
