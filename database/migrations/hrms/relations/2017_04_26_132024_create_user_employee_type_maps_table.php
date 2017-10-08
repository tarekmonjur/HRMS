<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserEmployeeTypeMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_employee_type_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('employee_type_id')->unsigned();
            $table->date('from_date')->comment='from_date n confirm_date are same for permanent emp';
            $table->date('to_date')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('employee_type_id')->references('id')->on('employee_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_employee_type_maps',function(Blueprint $table){
            $table->dropForeign('user_employee_type_maps_user_id_foreign');
            $table->dropForeign('user_employee_type_maps_employee_type_id_foreign');
        });
        
        Schema::dropIfExists('user_employee_type_maps');
    }
}
