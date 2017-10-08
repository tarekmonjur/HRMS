<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonWorkShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_work_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('common_shift_name',100);
            $table->time('common_shift_start_time');
            $table->time('common_shift_end_time');
            $table->time('common_late_count_time')->nullable();
            $table->boolean('common_work_shift_status')->default(1)->comment='0=inactive, 1=active';
            $table->integer('created_by')->default(0);    
            $table->integer('updated_by')->default(0);
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
        Schema::dropIfExists('common_work_shifts');
    }
}
