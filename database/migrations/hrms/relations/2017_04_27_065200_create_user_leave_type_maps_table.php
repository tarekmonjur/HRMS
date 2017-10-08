<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLeaveTypeMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_leave_type_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('leave_type_id')->unsigned();
            $table->integer('number_of_days')->nullable();
            $table->integer('active_from_year')->nullable();
            $table->integer('active_to_year')->nullable();
            $table->date('earn_leave_upgrade_date')->nullable();
            $table->boolean('status')->default(1)->comment='1=active 0=inactive';
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
        Schema::table('user_leave_type_maps',function(Blueprint $table){
            $table->dropForeign('user_leave_type_maps_user_id_foreign');
            $table->dropForeign('user_leave_type_maps_leave_type_id_foreign');
        });
        Schema::dropIfExists('user_leave_type_maps');
    }
}
