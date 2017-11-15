<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_references', function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('reference_name',50);
            $table->string('reference_email',50)->nullable();
            $table->string('reference_department',50)->nullable();
            $table->string('reference_organization',100);
            $table->string('reference_phone',20);
            $table->text('reference_address',20)->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_references',function (Blueprint $table){
            $table->dropForeign('employee_references_user_id_foreign');
        });
        Schema::dropIfExists('employee_references');
    }
}
