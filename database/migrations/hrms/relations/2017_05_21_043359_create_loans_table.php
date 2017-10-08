<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('loan_type_id')->unsigned();
            $table->enum('loan_aganist',['PF','salary']);
            $table->date('loan_start_date');
            $table->date('loan_end_date');
            $table->integer('loan_duration')->default(0)->comment='duration is months';
            $table->integer('loan_complete_duration')->default(0)->comment='total completed months.';
            $table->decimal('loan_amount',16,2);
            $table->decimal('loan_deduct_amount');
            $table->boolean('loan_status')->default(0);
            $table->text('loan_deduction_month')->nullable();
            $table->text('loan_remarks')->nullable();
            $table->integer('approved_by')->default(0);
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('loan_type_id')->references('id')->on('loan_types')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans',function(Blueprint $table){
            $table->dropForeign('loans_user_id_foreign');
            $table->dropForeign('loans_loan_type_id_foreign');
        });
        Schema::dropIfExists('loans');
    }
}
