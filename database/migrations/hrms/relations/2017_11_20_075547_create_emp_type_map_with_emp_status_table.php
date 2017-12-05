<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpTypeMapWithEmpStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emp_type_map_with_emp_status', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_emp_type_map_id')->unsigned();
            $table->integer('employee_status_id')->unsigned();
            $table->date('from_date')->comment='Status effective date';
            $table->date('to_date')->nullable();
            $table->text('remarks')->nullable();
            $table->string('document_files', 200)->nullable();
            $table->tinyInteger('pending_status')->nullable()->comment='0=pending 1=complete';
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('user_emp_type_map_id')->references('id')->on('user_employee_type_maps')->onDelete('cascade');
            $table->foreign('employee_status_id')->references('id')->on('employee_status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emp_type_map_with_emp_status',function(Blueprint $table){
            $table->dropForeign('emp_type_map_with_emp_status_user_emp_type_map_id_foreign');
            $table->dropForeign('emp_type_map_with_emp_status_employee_status_id_foreign');
        });
        
        Schema::dropIfExists('emp_type_map_with_emp_status');
    }
}
