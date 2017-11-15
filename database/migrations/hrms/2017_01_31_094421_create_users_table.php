<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('employee_no',100)->unique();
            $table->integer('employee_type_id')->unsigned();   
            $table->integer('branch_id')->unsigned();   
            $table->integer('designation_id')->unsigned();   
            $table->integer('unit_id')->unsigned();   
            $table->integer('supervisor_id')->unsigned()->nullable()->default(0);   
            $table->decimal('gross_salary',12,2)->nullable();   
            $table->decimal('basic_salary',12,2)->nullable();   
            $table->decimal('salary_in_cache',12,2)->nullable();   
            $table->date('effective_date')->nullable();   
            $table->string('first_name', 45);            
            $table->string('middle_name', 45)->nullable();  
            $table->string('last_name', 45);            
            $table->string('nick_name', 45)->nullable();            
            $table->string('email', 45)->unique();            
            $table->string('password');            
            $table->rememberToken();           
            $table->tinyInteger('status')->default(1);
            $table->string('mobile_number',16); 
            $table->string('photo',200)->nullable();  
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
        Schema::dropIfExists('users');
    }
}
