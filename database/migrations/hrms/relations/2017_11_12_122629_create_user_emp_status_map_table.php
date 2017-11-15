<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserEmpStatusMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_emp_status_map', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('employee_status_id')->unsigned();
            $table->date('from_date')->comment='Status effective date';
            $table->date('to_date')->nullable();
            $table->text('remarks')->nullable();
            $table->text('document_files')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('employee_status_id')->references('id')->on('employee_status')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_emp_status_map',function(Blueprint $table){
            $table->dropForeign('user_emp_status_map_user_id_foreign');
            $table->dropForeign('user_emp_status_map_employee_status_id_foreign');
        });
        
        Schema::dropIfExists('user_emp_status_map');
    }
}
