<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('leave_type_name', 70);
            $table->integer('leave_type_number_of_days')->nullable();
            $table->string('leave_type_effective_for')->nullable()->comment='employee type ids serialized';
            $table->text('level_type_details')->nullable();
            $table->boolean('level_type_is_remain')->default(0)->comment='carry to the next year or not=0';
            $table->boolean('level_type_include_holiday')->default(1)->comment='calculate with weekend and holiday=1 .. or not=0';
            $table->string('leave_type_active_from_year');
            $table->string('leave_type_active_to_year');
            $table->integer('leave_type_created_by')->nullable();
            $table->integer('leave_type_updated_by')->nullable();
            $table->boolean('leave_type_status')->default(1)->comment='active=1 deactive=0';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_types');
    }
}
