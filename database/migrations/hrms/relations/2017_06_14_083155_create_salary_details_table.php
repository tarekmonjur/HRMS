<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalaryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('salary_id')->unsigned();
            $table->string('salary_details_name');
            $table->enum('salary_details_type',['allowance','deduction']);
            $table->enum('salary_details_amount_type',['percent','fixed']);
            $table->decimal('salary_details_amount');
            $table->timestamps();

            $table->foreign('salary_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salary_details',function(Blueprint $table){
           $table->dropForeign('salary_details_user_id_foreign');
        });
        Schema::dropIfExists('salary_details');
    }
}
