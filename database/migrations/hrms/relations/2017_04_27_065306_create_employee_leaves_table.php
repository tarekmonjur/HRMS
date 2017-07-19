<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('leave_type_id')->unsigned();
            $table->date('employee_leave_from');
            $table->date('employee_leave_to');
            $table->float('employee_leave_total_days',8,2);
            $table->integer('employee_leave_application_subject')->nullable();
            $table->text('employee_leave_user_remarks')->nullable();
            $table->tinyInteger('employee_leave_noc_required')->nullable()->comment='1=required 0=not required';
            $table->tinyInteger('employee_leave_half_or_full')->default(1)->comment='1=full 2=half';
            $table->text('employee_leave_contact_address')->nullable();
            $table->string('employee_leave_contact_number')->nullable();
            $table->string('employee_leave_passport_no')->nullable();
            $table->integer('employee_leave_responsible_person')->nullable();
            $table->tinyInteger('employee_leave_responsible_person_status')->default(0)->comment='0=pending 1=accept 2=cancel';
            $table->integer('employee_leave_responsible_person_status_change_by')->nullable();
            $table->string('employee_leave_attachment')->nullable();
            $table->integer('employee_leave_supervisor')->nullable();
            $table->integer('employee_leave_recommend_to')->nullable();
            $table->integer('employee_leave_approved_by')->nullable();
            $table->text('employee_leave_approval_remarks')->nullable();
            $table->date('employee_leaves_approval_date')->nullable();
            $table->boolean('employee_leave_status')->default(1)->comment='1=pending 2=forward 3=approved 4=cancel';
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_leaves',function(Blueprint $table){
            $table->dropForeign('employee_leaves_user_id_foreign');
            $table->dropForeign('employee_leaves_leave_type_id_foreign');
        });
        
        Schema::dropIfExists('employee_leaves');
    }
}
