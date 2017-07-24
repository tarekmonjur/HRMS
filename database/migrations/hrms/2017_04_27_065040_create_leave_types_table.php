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
            $table->tinyInteger('leave_type_valid_after_months')->nullable();
            $table->text('leave_type_details')->nullable();
            $table->tinyInteger('leave_type_with_out_pay')->default(0)->comment='without pay=1 .. normal/with pay = 0';
            $table->boolean('leave_type_is_earn_leave')->default(0)->comment='earn leave=1 .. not earn leave = 0';
            $table->boolean('leave_type_is_sellable')->default(0)->comment='earn sellable=1 .. not sellable = 0';
            $table->integer('leave_type_max_sell_limit')->nullable();
            $table->boolean('leave_type_is_remain')->default(0)->comment='carry to the next year CF=1 or not=0';
            $table->integer('leave_type_max_remain_limit')->nullable();
            $table->boolean('leave_type_include_holiday')->comment='calculate with weekend and holiday=1 .. or not=0';
            $table->string('leave_type_active_from_year');
            $table->string('leave_type_active_to_year');
            $table->integer('leave_type_created_by')->nullable();
            $table->integer('leave_type_updated_by')->nullable();
            $table->boolean('leave_type_status')->default(1)->comment='active=1 deactive=0';
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
        Schema::dropIfExists('leave_types');
    }
}
