<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceTimesheetArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_timesheet_archives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->date('date');
            $table->tinyInteger('observation')->default('0')->comment='0=absent,1=present,2=leave,3=holiday,4=weekend';
            $table->time('in_time');
            $table->time('out_time');
            $table->decimal('total_work_hour',4,2);
            $table->string('leave_type')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_timesheet_archives');
    }
}