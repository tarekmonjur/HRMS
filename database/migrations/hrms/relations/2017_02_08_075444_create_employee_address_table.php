<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_address', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('present_postoffice')->nullable();
            $table->integer('present_policestation_id')->unsigned()->nullable();
            $table->integer('present_district_id')->unsigned()->nullable();
            $table->integer('present_division_id')->unsigned()->nullable();
            $table->string('present_address')->nullable();
            
            $table->string('permanent_postoffice')->nullable();
            $table->integer('permanent_policestation_id')->unsigned()->nullable();
            $table->integer('permanent_district_id')->unsigned()->nullable();
            $table->integer('permanent_division_id')->unsigned()->nullable();
            $table->string('permanent_address')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->foreign('present_policestation_id')->references('id')->on('police_stations')->onDelete('restrict');
            $table->foreign('present_district_id')->references('id')->on('districts')->onDelete('restrict');
            $table->foreign('present_division_id')->references('id')->on('divisions')->onDelete('restrict');

            $table->foreign('permanent_policestation_id')->references('id')->on('police_stations')->onDelete('restrict');
            $table->foreign('permanent_district_id')->references('id')->on('districts')->onDelete('restrict');
            $table->foreign('permanent_division_id')->references('id')->on('divisions')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_address',function (Blueprint $table){
            $table->dropForeign('employee_address_user_id_foreign');
            $table->dropForeign('employee_address_present_policestation_id_foreign');
            $table->dropForeign('employee_address_present_district_id_foreign');
            $table->dropForeign('employee_address_present_division_id_foreign');
            $table->dropForeign('employee_address_permanent_policestation_id_foreign');
            $table->dropForeign('employee_address_permanent_district_id_foreign');
            $table->dropForeign('employee_address_permanent_division_id_foreign');
        });
        Schema::dropIfExists('employee_address');
    }
}
